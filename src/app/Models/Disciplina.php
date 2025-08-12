<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disciplina extends Model
{
    use HasFactory;
    
    protected $table = 'disciplina';
    
    protected $fillable = [
        'nome',
        'periodo'
    ];
    
    /**
     * Relacionamento many-to-many com IntencaoMatricula
     */
    public function intencoesMatricula()
    {
        return $this->belongsToMany(
            IntencaoMatricula::class,
            'intencao_matricula_disciplina',
            'disciplina_id',
            'intencao_matricula_id'
        )->withTimestamps();
    }
    
    /**
     * Relacionamento direto com a tabela pivot
     */
    public function intencaoMatriculaDisciplinas()
    {
        return $this->hasMany(IntencaoMatriculaDisciplina::class, 'disciplina_id');
    }
    
    /**
     * Método para verificar se está associada a uma intenção
     */
    public function estaAssociadaA($intencaoMatriculaId): bool
    {
        return IntencaoMatriculaDisciplina::associacaoExiste($intencaoMatriculaId, $this->id);
    }
    
    // Validação adicional para o período
    public function setPeriodoAttribute($value)
    {
        if ($value < 1 || $value > 10) {
            throw new \InvalidArgumentException('O período deve estar entre 1 e 12');
        }
        
        $this->attributes['periodo'] = $value;
    }
}