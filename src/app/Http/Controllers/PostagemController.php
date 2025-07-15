<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostagemRequest;
use App\Models\Aluno;
use App\Models\ArquivoPostagem;
use App\Models\Banca;
use App\Models\ImagemPostagem;
use App\Models\Postagem;
use App\Models\Professor;
use App\Models\TipoPostagem;
use App\Models\PinnedPosts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

class PostagemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buscar = $request->buscar;

        $postagens = Postagem::when($buscar, function ($query, $buscar) {
            return $query->where('titulo', 'like', '%' . $buscar . '%');
        })->get();

        return view('postagem.index', ['postagens' => $postagens, 'buscar' => $buscar]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tipo_postagens = TipoPostagem::pluck('nome', 'id');
        $id = 1;
        $postagem = null;

        if (old() && URL::previous() === route('tcc.create')) {
            try {
                $banca = Banca::findOrFail(old('banca_id'));
                $professor = Professor::findOrFail(old('professor_id'));
                $aluno = Aluno::findOrFail(old('aluno_id'));

                $postagem = [
                    'titulo' => 'Convite TCC',
                    'texto' =>
                        'Aluno: ' . $aluno->nome . "\n" .
                        'Título: ' . old('titulo') . "\n" .
                        'Orientador: ' . $professor->servidor->nome . "\n" .
                        'Data: ' . date('d/m/Y', strtotime($banca->data)) . "\n" .
                        'Local: ' . $banca->local
                ];
            } catch (\Exception $e) {
                $postagem = null;
            }
        }

        return view('postagem.create', compact('tipo_postagens', 'id', 'postagem'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostagemRequest $request)
    {
        $postagem = new Postagem([
            'titulo' => $request->titulo,
            'texto' => $request->texto,
            'tipo_postagem_id' => $request->tipo_postagem_id,
            'menu_inicial' => $request->has('menu_inicial')
        ]);

        if ($request->has('menu_inicial')) {
            if ($request->hasFile("imagens")) {
                $imagens = $request->file("imagens");
                if(!Postagem::checkMainImageSize($imagens[0])){
                    return redirect()->back()->withInput()->with('error', 'A primeira imagem para exibição no menu inicial deve ter as dimensões de 2700 x 660.');
                }
            } else {
                return redirect()->back()->withInput()->with('error', 'Foi solicitado que aparecesse na tela inicial com destaque, mas nenhuma imagem foi cadastrada.');
            }
        }

        $postagem->save();

        if ($request->hasFile("imagens")) {
            $imagens = $request->file("imagens");

            foreach ($imagens as $imagem) {
                if ($imagem->isValid() && str_starts_with($imagem->getMimeType(), 'image/')) {
                    $imagemPostagem = new ImagemPostagem();
                    $imagemPostagem->postagem_id = $postagem->id;
                    $imagemPostagem->imagem = $imagem->store('ImagemPostagem/' . $postagem->id, 'public');
                    $imagemPostagem->save();
                } else {
                }
            }
        }

        if ($request->hasFile("arquivos")) {
            $arquivos = $request->file("arquivos");

            foreach ($arquivos as $arquivo) {
                if ($arquivo->isValid()) {
                    $arquivoPostagem = new ArquivoPostagem();
                    $arquivoPostagem->postagem_id = $postagem->id;
                    $arquivoPostagem->nome = $arquivo->getClientOriginalName();
                    $arquivoPostagem->path = $arquivo->store('ArquivoPostagem/' . $postagem->id, 'public');
                    $arquivoPostagem->save();
                } else {
                    Log::warning('Arquivo inválido detectado no upload de postagem: ' . $arquivo->getClientOriginalName());
                }
            }
        }

        return redirect('postagem')->with('success', 'Postagem Criada com Sucesso');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $postagem = Postagem::findOrFail($id);
        $tipo_postagens = TipoPostagem::pluck('nome', 'id');

        return view('postagem.edit', ['postagem' => $postagem, 'tipo_postagens' => $tipo_postagens]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostagemRequest $request, string $id)
    {
        $postagem = Postagem::findOrFail($id);

        $postagem->update([
            'titulo' => $request->titulo,
            'texto' => $request->texto,
            'tipo_postagem_id' => $request->tipo_postagem_id,
            'menu_inicial' => $request->has('menu_inicial')
        ]);

        if ($request->hasFile("imagens")) {
            $imagens = $request->file("imagens");

            foreach ($imagens as $imagem) {
                if ($imagem->isValid() && str_starts_with($imagem->getMimeType(), 'image/')) {
                    $imagemPostagem = new ImagemPostagem();
                    $imagemPostagem->postagem_id = $postagem->id;
                    $imagemPostagem->imagem = $imagem->store('ImagemPostagem/' . $postagem->id, 'public');
                    $imagemPostagem->save();
                }
            }
        }

        if ($request->hasFile("arquivos")) {
            $arquivos = $request->file("arquivos");

            foreach ($arquivos as $arquivo) {
                if ($arquivo->isValid()) {
                    $arquivoPostagem = new ArquivoPostagem();
                    $arquivoPostagem->postagem_id = $postagem->id;
                    $arquivoPostagem->nome = $arquivo->getClientOriginalName();
                    $arquivoPostagem->path = $arquivo->store('ArquivoPostagem/' . $postagem->id, 'public');
                    $arquivoPostagem->save();
                }
            }
        }

        return redirect('postagem')->with('success', 'Postagem Alterada com Sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $postagem = Postagem::findOrFail($id);

         foreach ($postagem->imagens as $imagem) {
             Storage::disk('public')->delete($imagem->imagem);
         }
         foreach ($postagem->arquivos as $arquivo) {
             Storage::disk('public')->delete($arquivo->path);
         }

        $postagem->delete();
        return back()->with('success', 'Postagem Excluída com Sucesso');
    }

    public function deleteImagem($id)
    {
        $imagem = ImagemPostagem::findOrFail($id);

        if (Storage::disk('public')->exists($imagem->imagem)) {
            Storage::disk('public')->delete($imagem->imagem);
        }
        $imagem->delete();
        return back()->with('success', 'Imagem excluída com sucesso.');
    }

    public function deleteArquivo($id)
    {
        $arquivo = ArquivoPostagem::findOrFail($id);

        if (Storage::disk('public')->exists($arquivo->path)) {
            Storage::disk('public')->delete($arquivo->path);
        }
        $arquivo->delete();
        return back()->with('success', 'Arquivo excluído com sucesso.');
    }

    public function display()
    {
        $postagens = Postagem::orderBy('created_at', 'desc')->get();
        $postagens_9 = Postagem::orderBy('created_at', 'desc')->paginate(9);

        return view('postagem.display', ['postagens' => $postagens, 'postagens_9' => $postagens_9]);
    }

    public function show(string $id)
    {
        $postagem = Postagem::findOrFail($id);
        $tipo_postagem = TipoPostagem::findOrFail($postagem->tipo_postagem_id);
        dd($postagem->imagens->isEmpty());
        return view('postagem.show', ['postagem' => $postagem, 'tipo_postagem' => $tipo_postagem]);
    }

    public function togglePin(postagem $postagem)
    {
        $pinnedpost = PinnedPosts::find($postagem->id);
        $imagens = $postagem->imagens;

        if ($imagens->isEmpty()) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => "nenhuma imagem encontrada para essa postagem.",
                'imagens' => $imagens,
            ]);
        }

        $imagem = $imagens->first();
        $imagempath = $imagem->imagem;

        if (!postagem::checkMainImageSize($imagempath)) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => "a imagem principal não possui as dimensões necessárias.",
                'imagens' => $imagens,
            ]);
        }

        if ($pinnedpost) {
            $pinnedpost->delete();
            $status = 'unpinned';
            $message = "postagem '{$postagem->titulo}' desfixada com sucesso.";
        } else {
            pinnedposts::create(['postagem_id' => $postagem->id]);
            $status = 'pinned';
            $message = "postagem '{$postagem->titulo}' fixada com sucesso.";
        }

        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => $message,
            'imagens' => $imagens,
        ]);
    }
}
