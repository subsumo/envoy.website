<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Utility\Hash;


class BrochuresController extends \App\Controller\BrochuresController {

  public $helpers = array('ImageResize');

  
 

  public function index() {
    
    $this->paginate = [
        'limit' => 25,
        'contain' => ['Suppliers'],
        'sortWhitelist'=>['Suppliers.company','sku','name','is_french','max_order','qty_skid','qty_box','inv_balance','Brochures.created']
      ];
    
    $this->set('brochures', $this->paginate());
  }

  public function view($id = null) {
       if (!empty($this->request->data)) { 
    $skufind = $this->request->data; 
    $skufind1 = $this->Brochures->findBySku($skufind['data']['barcodes']);
   }
   else {
    $skufind1 = $this->Brochures->findById($id);
   }

   $skufind2 = $skufind1->all();
    
   if ($skufind1->isEmpty()) {
    $this->Flash->set(__('Invalid brochure'));
    $id=300;
   }
   else { 
   $id=$skufind2->first()->id;
   $this->set('brochure', $this->Brochures->get($id, [
    'contain' => ['Suppliers','Images','Racks']
    ]));
    $broch1=$this->Brochures->get($id, [
    'contain' => ['Racks']])->toArray();

     $sortedracks = Hash::sort($broch1['racks'], '{n}.rack_number', 'asc');
      $this->set(compact('sortedracks'));
       }
  }

  public function add() {
  
    $broch=$this->request->data;

    if (!empty($this->request->data)) {

      //hacking in blank default values since there is no inputs for the following
      $this->request->data['location'] = '';
      $this->request->data['restrict_access'] = 0;
      $this->request->data['max_restricted_qty'] = 0;

      if ($this->request->data['image']['tmp_name'] != '') {
        $file = new File($this->request->data['image']['tmp_name']);
        $filename = $this->request->data['image']['name'];
        $data = $file->read();
        $file->close();
        $file = new File(WWW_ROOT.'/img/brochures/'.$filename,true);
        $file->write($data);
        $file->close();

        unset($this->request->data['image']);
        $image = [
          'filename' => $filename,
          'caption' => $filename
        ];

        $image = $this->Brochures->Images->newEntity($image);
        if ($image = $this->Brochures->Images->save($image)) {
          $this->Flash->set(__('The brochure image could not be saved. Please, try again.'));
        }

        
        $this->request->data['image_id'] ='';

      } else {
        $image = '';
      }

      try {
      $brochure = $this->Brochures->newEntity($this->request->data,['accessibleFields'=>['sku'=>true],'contain'=>'Images']);  


      if ($image) {
        $brochure->image = $image;
      }

      if ($brochure = $this->Brochures->save($brochure)) {
        $this->Flash->set(__('The brochure has been saved'));
        //     $this->_notifyWarehouse($broch);
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Flash->set(__('The brochure could not be saved. Please, try again.'));
      }
    } catch (Exception $e) {
      $this->Flash->set(__('The brochure could not be saved. Please, try again.'));
    }
    }


    $images = $this->Brochures->Images->find('list', array('fields' => array('id', 'caption')));
    //$images['0'] = "None";
    $this->LoadModel('Suppliers');
    $suppliers = $this->Suppliers->find('list', 
      [
        'fields' => array('company','id'),
        'order'=>['Suppliers.company']
      ]);

    //$suppliers['0'] = "None";
    $this->set(compact('images', 'suppliers'));

  }

  public function edit($id = null) {

    $broch=$this->request->data;
  
    if (!$id && empty($this->request->data)) {
      $this->Flash->set(__('Invalid brochure'));
      $this->redirect(array('action' => 'index'));
    }
    if (!empty($this->request->data)) {

      $this->Brochures->BrochureOrderAmts->deleteAll(['brochure_id' => $id]);
      $brochureOrderAmts = [];      

      foreach ($this->request->data['brochure_order_amts'] as $brochurOrderAmt) {
        if ($brochurOrderAmt['quantity'] <> null) {
          array_push($brochureOrderAmts, $this->Brochures->BrochureOrderAmts->newEntity($brochurOrderAmt));
        }
      }

      $this->request->data['brochure_order_amts'] = array(null);

      $brochure = $this->Brochures->newEntity($this->request->data);
      $brochure->brochure_order_amts = $brochureOrderAmts;
      
     

      if ($this->Brochures->save($brochure)) {
        $this->Flash->set(__('The brochure has been saved'));
        //     $this->_notifyWarehouse($broch);
        $this->redirect($this->referer());
      } else {
        $this->Flash->set(__('The brochure could not be saved. Please, try again.'));
        $this->redirect($this->referer());
      }
    }


    if (empty($this->request->data)) {
      $this->request->data = $this->Brochures->findById($id)->contain(['BrochureOrderAmts','Racks'])->first()->toArray(); 
      
      $broch1=$this->request->data; 
      $sortedracks = Hash::sort($broch1['racks'], '{n}.rack_number', 'asc');
      $this->set(compact('sortedracks'));

    }

    $images = $this->Brochures->Images->find('list', array('fields' => array('id', 'caption'),'order'=>['Images.caption']));
    
    
    $suppliers = $this->Brochures->Suppliers->find('list', array('fields' => array('id', 'company'),'order'=>['Suppliers.company']));

    $this->set(compact('images', 'suppliers'));

    $brochurelinks = $this->request->data['ebrochure'];
    $this->set(compact('brochurelinks'));

  }

  public function delete($id = null) {
    if (!$id) {
      $this->Flash->set(__('Invalid id for brochure'));
      $this->redirect(array('action' => 'index'));
    }
    $brochure = $this->Brochures->get($id);

    if ($this->Brochures->delete($brochure)) {
      $this->Flash->set(__('Brochure deleted'));
      $this->redirect(array('action' => 'index'));
    }
    $this->Flash->set(__('Brochure was not deleted'));
    $this->redirect(array('action' => 'index'));
  }
  
 

}

?>