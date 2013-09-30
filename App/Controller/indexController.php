<?php
class indexController extends Controller
{
    public function index()
    {
        echo 'this is a controller test<br />';
        $model = $this->model('index');
        $model->index();
        $model->test();
        
        $this->assign('name', 'niulingyun');
        $this->display('index');
    }
}