<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table      = 'comments';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['name', 'text', 'date', 'created_at'];
    
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    
    protected $validationRules = [
        'name' => 'required|valid_email|regex_match[/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/]|max_length[255]',
        'text' => 'required',
        'date' => 'required|max_length[100]',
    ];
    
    protected $validationMessages = [
        'name' => [
            'required'    => 'Email обязателен для заполнения',
            'valid_email' => 'Введите корректный email адрес',
        ],
        'text' => [
            'required' => 'Текст комментария обязателен',
        ],
        'date' => [
            'required' => 'Дата обязательна',
        ],
    ];
}
