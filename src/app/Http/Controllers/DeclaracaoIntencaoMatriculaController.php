<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeclaracaoIntencaoMatriculaRequest;
use App\Models\Aluno;
use App\Models\IntencaoMatricula;
use App\Models\Disciplina;
use App\Models\DeclaracaoIntencaoMatricula;
use App\Models\DeclaracaoIntencaoMatriculaDisciplina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeclaracaoIntencaoMatriculaController extends Controller
{
    /**
     * Construtor que restringe acesso apenas a alunos
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            // Verifica se o usuário é um aluno
            if (!Auth::user()->hasRole('aluno')) {
                return redirect()->route('postagem.display')->with('error', 'Acesso não autorizado.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtém o aluno autenticado
        $aluno = Auth::user()->aluno;
        
        // Busca todas as declarações do aluno
        $declaracoes = DeclaracaoIntencaoMatricula::where('aluno_id', $aluno->id)
                                                 ->orderBy('created_at', 'desc')
                                                 ->with('intencoesMatricula.disciplinas')
                                                 ->get();
        
        return view('declaracao_intencao_matricula.index', compact('aluno', 'declaracoes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtém o aluno autenticado
        $aluno = Auth::user()->aluno;
        
        // Busca todas as intenções de matrícula disponíveis
        $intencoesMatricula = IntencaoMatricula::orderBy('ano', 'desc')
                                      ->orderBy('numero_periodo')
                                      ->get();
        
        return view('declaracao_intencao_matricula.create', compact('aluno', 'intencoesMatricula'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ano' => ['required', 'integer', 'min:2020', 'max:' . (date('Y') + 5)],
            'periodo' => ['required', 'integer', 'min:1', 'max:2'],
            'intencao_matricula_id' => ['required', 'exists:intencao_matricula,id'],
        ]);

        // Obtém o aluno autenticado
        $aluno = Auth::user()->aluno;

        // Cria a declaração de intenção de matrícula
        $declaracao = new DeclaracaoIntencaoMatricula();
        $declaracao->ano = $request->ano;
        $declaracao->periodo = $request->periodo;
        $declaracao->aluno_id = $aluno->id;
        $declaracao->save();

        // Não precisamos mais do relacionamento direto
        // Vamos apenas log para informar que a declaração foi criada
        \Log::info('Nova declaração de intenção criada:', [
            'declaracao_id' => $declaracao->id,
            'aluno_id' => $aluno->id,
            'ano' => $request->ano,
            'periodo' => $request->periodo,
            'intencao_matricula_id' => $request->intencao_matricula_id
        ]);

        return redirect()->route('declaracao_intencao_matricula.index')->with('success', 'Declaração de intenção de matrícula enviada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Obtém o aluno autenticado
        $aluno = Auth::user()->aluno;
        
        // Busca a declaração específica, garantindo que pertença ao aluno logado
        $declaracao = DeclaracaoIntencaoMatricula::where('id', $id)
                                              ->where('aluno_id', $aluno->id)
                                              ->with('intencoesMatricula.disciplinas')
                                              ->firstOrFail();
        
        return view('declaracao_intencao_matricula.show', compact('declaracao'));
    }
    
    /**
     * Exibe o formulário para selecionar disciplinas
     */
    public function selecionarDisciplinas()
    {
        // Obtém o aluno autenticado
        $aluno = Auth::user()->aluno;
        
        // Obtém o ano atual e o próximo
        $anoAtual = date('Y');
        $anos = [$anoAtual, $anoAtual + 1];
        
        // Períodos disponíveis (1 ou 2)
        $periodos = [1, 2];
        
        return view('declaracao_intencao_matricula.selecionar_disciplinas', compact('aluno', 'anos', 'periodos'));
    }
    
    /**
     * Busca as disciplinas para um determinado ano e período
     */
    public function buscarDisciplinas(Request $request)
    {
        $request->validate([
            'ano' => ['required', 'integer', 'min:2020', 'max:' . (date('Y') + 5)],
            'periodo' => ['required', 'integer', 'min:1', 'max:2'],
        ]);
        
        // Obtém o aluno autenticado
        $aluno = Auth::user()->aluno;
        
        if (!$aluno) {
            return response()->json([
                'error' => true,
                'message' => 'Não foi possível encontrar o aluno associado ao seu usuário.'
            ], 404);
        }


        // Busca a intenção de matrícula correspondente
        $intencaoMatricula = IntencaoMatricula::where('ano', $request->ano)
                                            ->where('numero_periodo', $request->periodo)
                                            ->first();
        
        if (!$intencaoMatricula) {
            return response()->json([
                'error' => true,
                'message' => 'Não foi encontrada intenção de matrícula para o ano e período selecionados.'
            ], 404);
        }
        
        // Busca as disciplinas associadas à intenção de matrícula
        $disciplinas = $intencaoMatricula->disciplinas()->orderBy('periodo')->get();
        
        // Busca a declaração existente do aluno para esta intenção (se existir)
        $declaracaoExistente = DeclaracaoIntencaoMatricula::where('aluno_id', $aluno->id)
                                                      ->where('ano', $request->ano)
                                                      ->where('periodo', $request->periodo)
                                                      ->first();
        
        // Obtém os IDs das disciplinas já selecionadas
        $disciplinasSelecionadas = [];
        if ($declaracaoExistente) {
            $disciplinasSelecionadas = $declaracaoExistente->disciplinasDeclaradas()
                                                      ->pluck('disciplina_id')
                                                      ->toArray();
        }

        // Agrupa as disciplinas por período
        $disciplinasPorPeriodo = [];
        foreach ($disciplinas as $disciplina) {
            if ($disciplina->optativa) {
                // Adiciona à categoria de optativas
                if (!isset($disciplinasPorPeriodo['optativas'])) {
                    $disciplinasPorPeriodo['optativas'] = [];
                }
                $disciplinasPorPeriodo['optativas'][] = [
                    'id' => $disciplina->id,
                    'nome' => $disciplina->nome,
                    'periodo' => null,
                    'optativa' => true,
                    'intencao_matricula_id' => $intencaoMatricula->id,
                    'selecionada' => in_array($disciplina->id, $disciplinasSelecionadas)
                ];
            } else {
                // Adiciona ao período correspondente
                $periodo = $disciplina->periodo;
                if (!isset($disciplinasPorPeriodo[$periodo])) {
                    $disciplinasPorPeriodo[$periodo] = [];
                }
                $disciplinasPorPeriodo[$periodo][] = [
                    'id' => $disciplina->id,
                    'nome' => $disciplina->nome,
                    'periodo' => $disciplina->periodo,
                    'optativa' => false,
                    'intencao_matricula_id' => $intencaoMatricula->id,
                    'selecionada' => in_array($disciplina->id, $disciplinasSelecionadas)
                ];
            }
        }
        
        return response()->json([
            'intencao_matricula_id' => $intencaoMatricula->id,
            'disciplinas' => $disciplinasPorPeriodo,
            'tem_selecao_anterior' => !empty($disciplinasSelecionadas),
            'disciplinas_selecionadas' => $disciplinasSelecionadas
        ]);
    }
    
    /**
     * Salva as disciplinas selecionadas pelo aluno
     */
    public function salvarDisciplinas(Request $request)
    {
        $request->validate([
            'ano' => ['required', 'integer', 'min:2020', 'max:' . (date('Y') + 5)],
            'periodo' => ['required', 'integer', 'min:1', 'max:2'],
            'intencao_matricula_id' => ['required', 'exists:intencao_matricula,id'],
            'disciplinas' => ['required', 'array', 'min:1'],
            'disciplinas.*' => ['required', 'exists:disciplina,id'],
        ]);
        
        // Obtém o aluno autenticado
        $aluno = Auth::user()->aluno;
        
        if (!$aluno) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Não foi possível encontrar o aluno associado ao seu usuário.');
        }
        
        // Verifica se há disciplinas selecionadas
        if (empty($request->disciplinas)) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Você precisa selecionar pelo menos uma disciplina.');
        }
        
        // Valida se as disciplinas existem no banco de dados
        $disciplinasIds = $request->disciplinas;
        $disciplinasExistentes = Disciplina::whereIn('id', $disciplinasIds)
            ->pluck('id')
            ->toArray();
            
        $disciplinasInvalidas = array_diff($disciplinasIds, $disciplinasExistentes);
        if (!empty($disciplinasInvalidas)) {
            \Log::warning('Tentativa de selecionar disciplinas inexistentes:', [
                'disciplinas_invalidas' => $disciplinasInvalidas
            ]);
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Algumas disciplinas selecionadas não existem no sistema. Por favor, atualize a página e tente novamente.');
        }
        
        \Log::info('Iniciando salvar disciplinas:', [
            'aluno_id' => $aluno->id,
            'ano' => $request->ano,
            'periodo' => $request->periodo,
            'disciplinas' => $request->disciplinas
        ]);
        
        DB::beginTransaction();
        
        try {
            // Verifica se já existe uma declaração para este aluno, ano e período
            $declaracao = DeclaracaoIntencaoMatricula::where('aluno_id', $aluno->id)
                                                  ->where('ano', $request->ano)
                                                  ->where('periodo', $request->periodo)
                                                  ->first();
            
            // Se não existir, cria uma nova declaração
            if (!$declaracao) {
                $declaracao = new DeclaracaoIntencaoMatricula();
                $declaracao->ano = $request->ano;
                $declaracao->periodo = $request->periodo;
                $declaracao->aluno_id = $aluno->id;
                $declaracao->save();
                
                \Log::info('Nova declaração criada:', [
                    'id' => $declaracao->id,
                    'aluno_id' => $aluno->id,
                    'ano' => $request->ano,
                    'periodo' => $request->periodo
                ]);
            } else {
                // Se existir, limpa todas as disciplinas associadas à declaração
                try {
                    // Usa o relacionamento para excluir as disciplinas
                    $qtdExcluidas = $declaracao->disciplinasDeclaradas()->delete();
                    
                    \Log::info('Disciplinas excluídas:', [
                        'declaracao_id' => $declaracao->id,
                        'quantidade_excluidas' => $qtdExcluidas
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Erro ao excluir disciplinas antigas:', [
                        'message' => $e->getMessage(),
                        'declaracao_id' => $declaracao->id
                    ]);
                    throw $e; // Re-throw to be caught by the outer try-catch
                }
            }
            
            // Verifica se a intenção de matrícula existe
            $intencaoMatricula = IntencaoMatricula::find($request->intencao_matricula_id);
            if (!$intencaoMatricula) {
                \Log::error('Intenção de matrícula não encontrada:', [
                    'intencao_matricula_id' => $request->intencao_matricula_id
                ]);
                throw new \Exception("Intenção de matrícula com ID {$request->intencao_matricula_id} não foi encontrada.");
            }
            
            \Log::info('Relação com intenção de matrícula verificada:', [
                'declaracao_id' => $declaracao->id,
                'intencao_matricula_id' => $request->intencao_matricula_id
            ]);
            
            // Salva as disciplinas selecionadas
            $disciplinasInseridas = 0;
            $disciplinasParaInserir = [];
            
            // Log the selected disciplines for debugging
            \Log::info('Disciplinas recebidas no request:', [
                'disciplinas' => $request->disciplinas
            ]);
            
            foreach ($request->disciplinas as $disciplinaId) {
                // Ensure disciplinaId is a valid integer and handle empty strings or null values
                if (empty($disciplinaId) || !is_numeric($disciplinaId)) {
                    \Log::warning('Ignorando disciplina_id inválido:', [
                        'valor' => $disciplinaId,
                        'tipo' => gettype($disciplinaId)
                    ]);
                    continue;
                }
                
                $disciplinaId = (int) $disciplinaId;
                
                // Verifica se a disciplina existe
                $disciplina = Disciplina::find($disciplinaId);
                if (!$disciplina) {
                    \Log::warning('Ignorando disciplina inexistente:', [
                        'disciplina_id' => $disciplinaId
                    ]);
                    continue;
                }
                
                // Em vez de criar objetos, vamos apenas preparar os dados para usar com o método criarComValidacao
                $disciplinasParaInserir[] = [
                    'disciplina_id' => $disciplinaId,
                    'intencao_matricula_id' => $request->intencao_matricula_id,
                    'declaracao_id' => $declaracao->id
                ];
                $disciplinasInseridas++;
                
                // Log each insertion for debugging
                \Log::info('Preparando disciplina para inserção:', [
                    'disciplina_id' => $disciplinaId,
                    'declaracao_id' => $declaracao->id,
                    'intencao_matricula_id' => $request->intencao_matricula_id
                ]);
            }
            
            // Insere todas as disciplinas de uma vez
            if (!empty($disciplinasParaInserir)) {
                try {
                    // Log detalhado antes da inserção
                    \Log::info('Preparando para inserir disciplinas:', [
                        'declaracao_id' => $declaracao->id,
                        'quantidade' => count($disciplinasParaInserir)
                    ]);
                    
                    // Verifica novamente se a declaração existe no banco
                    $declaracaoExiste = DeclaracaoIntencaoMatricula::find($declaracao->id);
                        
                    if (!$declaracaoExiste) {
                        \Log::error('Declaração não encontrada no banco antes da inserção:', [
                            'declaracao_id' => $declaracao->id
                        ]);
                        throw new \Exception("Declaração com ID {$declaracao->id} não foi encontrada no banco de dados.");
                    }

                    // Inserção dos objetos usando o método aprimorado
                    foreach ($disciplinasParaInserir as $index => $disciplinaData) {
                        // Use o método criarComValidacao em vez do save
                        $resultado = DeclaracaoIntencaoMatriculaDisciplina::criarComValidacao(
                            $disciplinaData['declaracao_id'],
                            $disciplinaData['intencao_matricula_id'],
                            $disciplinaData['disciplina_id']
                        );
                        
                        if ($resultado) {
                            \Log::info("Disciplina {$index} inserida com sucesso:", [
                                'disciplina_id' => $disciplinaData['disciplina_id'],
                                'disciplina_declarada_id' => $resultado->id
                            ]);
                        } else {
                            \Log::warning("Falha ao inserir disciplina {$index}:", [
                                'disciplina_id' => $disciplinaData['disciplina_id']
                            ]);
                        }
                    }
                    
                    \Log::info('Todas as disciplinas foram processadas', [
                        'quantidade' => count($disciplinasParaInserir)
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Erro ao inserir disciplinas:', [
                        'message' => $e->getMessage(),
                        'codigo' => $e->getCode()
                    ]);
                    throw $e; // Re-throw to be caught by the outer try-catch
                }
            }
            
            \Log::info('Disciplinas inseridas:', [
                'declaracao_id' => $declaracao->id,
                'quantidade_inseridas' => $disciplinasInseridas,
                'disciplinas' => $request->disciplinas
            ]);
            
            DB::commit();
            
            // Redirecionar para a mesma página, mas com parâmetros na URL para permitir
            // a recarga automática das disciplinas
            \Log::info('Redirecionando após salvar disciplinas', [
                'ano' => $request->ano,
                'periodo' => $request->periodo
            ]);
            
            return redirect()->route('declaracao_intencao_matricula.selecionar_disciplinas', [
                'ano' => $request->ano,
                'periodo' => $request->periodo
            ])->with('success', 'Disciplinas declaradas com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Erro ao salvar disciplinas:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => [
                    'ano' => $request->ano,
                    'periodo' => $request->periodo,
                    'aluno_id' => $aluno->id,
                    'intencao_matricula_id' => $request->intencao_matricula_id,
                    'disciplinas_count' => count($request->disciplinas ?? [])
                ]
            ]);
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Erro ao salvar disciplinas: ' . $e->getMessage());
        }
    }
}