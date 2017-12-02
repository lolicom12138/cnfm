<?php
namespace Statistique\Controller;
use Statistique\Common\Controller\Base;
class ProductController extends Base {
    public function index(){
       if(!testClassified($this->user,"statistiqueProduct")){
       		$this->error("权限不足");
       		return;
       }
       $supplierModel = D('Supplier');
       $listSupplier = $supplierModel->select();
       $shopModel = D('Shop');
       $listShop = $shopModel->select();
       $listClass = M('productClass')->select();
       $this->assign("listShop",$listShop);
       $this->assign("listSupplier",$listSupplier);
       $this->assign("listClass",$listClass);
       $this->display();
    }
}