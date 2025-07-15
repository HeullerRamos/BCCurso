<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postagem extends Model {

    protected $table = 'postagem';
    
    protected $fillable = [
        'titulo',
        'texto',
        'tipo_postagem_id',
        'menu_inicial',
    ];

    public function imagens(){
        return $this->hasMany(ImagemPostagem::class);
    }

    public function capa(){
        return $this->hasOne(CapaPostagem::class);
    }

    public function arquivos(){
        return $this->hasMany(ArquivoPostagem::class);
    }

    public function isPinned(){
      return PinnedPosts::where('postagem_id', $this->id)->exists();
    }


    public static function checkMainImageSize($imagem)
    {
        $dimensions = getimagesize($imagem);
        if ($dimensions === false) {
            return false;
        }

        $largura = $dimensions[0];
        $altura = $dimensions[1];

        return $largura === 2700 && $altura === 660;
    }
}
