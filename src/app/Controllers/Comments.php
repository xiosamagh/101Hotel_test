<?php

namespace App\Controllers;

use App\Models\CommentModel;

class Comments extends BaseController
{
    protected $commentModel;
    
    public function __construct()
    {
        $this->commentModel = new CommentModel();
    }
    
    public function index()
    {
        return view('comments/index');
    }
    
    public function getComments()
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $sortBy = $this->request->getGet('sort') ?? 'id';
            $sortOrder = $this->request->getGet('order') ?? 'desc';
            
            $perPage = 3;
            
            $allowedSort = ['id', 'date'];
            if (!in_array($sortBy, $allowedSort)) {
                $sortBy = 'id';
            }
            
            $allowedOrder = ['asc', 'desc'];
            if (!in_array($sortOrder, $allowedOrder)) {
                $sortOrder = 'desc';
            }
            
            $comments = $this->commentModel
                ->orderBy($sortBy, $sortOrder)
                ->paginate($perPage, 'default', $page);
            
            $pager = $this->commentModel->pager;
            
            return $this->response->setJSON([
                'success' => true,
                'comments' => $comments ?? [],
                'pager' => [
                    'currentPage' => $pager->getCurrentPage(),
                    'totalPages' => $pager->getPageCount(),
                    'perPage' => $perPage,
                    'total' => $pager->getTotal(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function create()
    {
        $data = [
            'name' => $this->request->getPost('name'),
            'text' => $this->request->getPost('text'),
            'date' => $this->request->getPost('date'),
        ];
        
        if ($this->commentModel->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Комментарий успешно добавлен',
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->commentModel->errors(),
            ]);
        }
    }
    
    public function delete($id)
    {
        if ($this->commentModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Комментарий удален',
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ошибка при удалении',
            ]);
        }
    }
}
