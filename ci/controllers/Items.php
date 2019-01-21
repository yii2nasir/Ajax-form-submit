<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Items extends CI_Controller {


   /**
    * Get All Data from this method.
    *
    * @return Response
   */
  function role(){
      return [
          'title'=>'text',
          'description'=>'textarea',

      ];
  }
  function item(){
      $this->load->view('welcome_message');
  }
   public function index()
   {
       $this->load->database();
       $this->load->view('welcome_message');
       $tit=$this->role();
      // var_dump($tit);exit;
       if(!empty($this->input->get("search"))){
        $first=true;
          foreach($tit as $v){
              if($first){
                $this->db->like($v, $this->input->get("search"));
                $first=false;
              }else{
                $this->db->or_like($v, $this->input->get("search"));
                }
            }
        }

       $this->db->limit(5, ($this->input->get("page",1) - 1) * 5);
      // echo "hi";
       $query = $this->db->get("items");
       $data['data'] = $query->result();
       $data['total'] = $this->db->count_all("items");

       
       echo json_encode($data);
   }


   /**
    * Store Data from this method.
    *
    * @return Response
   */
   public function store()
   {
       $this->load->database();


       $insert = $this->input->post();
       $this->db->insert('items', $insert);
       $id = $this->db->insert_id();
       $q = $this->db->get_where('items', array('id' => $id));


       echo json_encode($q->row());
    }


   /**
    * Edit Data from this method.
    *
    * @return Response
   */
   public function edit($id)
   {
       $this->load->database();
       $q = $this->db->get_where('items', array('id' => $id));
       echo json_encode($q->row());
   }


   /**
    * Update Data from this method.
    *
    * @return Response
   */
   public function update($id)
   {
       $this->load->database();


       $insert = $this->input->post();
       $this->db->where('id', $id);
       $this->db->update('items', $insert);
       $q = $this->db->get_where('items', array('id' => $id));


       echo json_encode($insert);
    }


   /**
    * Delete Data from this method.
    *
    * @return Response
   */
   public function delete($id)
   {
       $this->load->database();


       $this->db->where('id', $id);
       $this->db->delete('items');


       echo json_encode(['success'=>true]);
    }

    function ajax_creator(){
        $dy=" rows = rows + '<td>'+value.title+'</td>';
        rows = rows + '<td>'+value.description+'</td>';
       ";
       $r236=" data:{title:title, description:description}";
        $js="var page = 1;
        var current_page = 1;
        var total_page = 0;
        var is_ajax_fire = 0;
        
        
        manageData();
        
        
        /* manage data list */
        function manageData() {
           $.ajax({
              dataType: 'json',
              url: url,
              data: {page:page}
            }).done(function(data){
        
        
               total_page = data.total % 5;
               current_page = page;
        
        
               $('#pagination').twbsPagination({
                    totalPages: total_page,
                    visiblePages: current_page,
                    onPageClick: function (event, pageL) {
        
        
                        page = pageL;
        
        
                        if(is_ajax_fire != 0){
                           getPageData();
                        }
                    }
                });
        
        
                manageRow(data.data);
        
        
                is_ajax_fire = 1;
        
        
           });
        }
        
        
        /* Get Page Data*/
        function getPageData() {
        
        
            $.ajax({
               dataType: 'json',
               url: url,
               data: {page:page}
            }).done(function(data){
        
        
               manageRow(data.data);
        
        
            });
        
        
        }
        
        
        /* Add new Item table row */
        function manageRow(data) {
        
        
            var	rows = '';
        
        
            $.each( data, function( key, value ) {
        
        
                rows = rows + '<tr>';
                rows = rows + '<td>'+value.title+'</td>';
                rows = rows + '<td>'+value.description+'</td>';
                rows = rows + '<td data-id='+value.id+'>';
                rows = rows + '<button data-toggle=\"modal\" data-target=\"#edit-item\" class=\"btn btn-primary edit-item\">Edit</button> ';
                rows = rows + '<button class=\"btn btn-danger remove-item\">Delete</button>';
                rows = rows + '</td>';
                rows = rows + '</tr>';
        
        
            });
        
        
            $(\"tbody\").html(rows);
        
        
        }
        
        
        /* Create new Item */
        $(\".crud-submit\").click(function(e){
        
        
            e.preventDefault();
        
        
            var form_action = $(\"#create-item\").find(\"form\").attr(\"action\");
            var title = $(\"#create-item\").find(\"input[name='title']\").val();
            var description = $(\"#create-item\").find(\"textarea[name='description']\").val();
        
        
            $.ajax({
                dataType: 'json',
                type:'POST',
                url: form_action,
                data:{title:title, description:description}
            }).done(function(data){
        
        
                getPageData();
                $(\".modal\").modal('hide');
                toastr.success('Item Created Successfully.', 'Success Alert', {timeOut: 5000});
        
        
            });
        
        
        });
        
        
        /* Remove Item */
        $(\"body\").on(\"click\",\".remove-item\",function(){
        
        
            var id = $(this).parent(\"td\").data('id');
            var c_obj = $(this).parents(\"tr\");
        
        
            $.ajax({
                dataType: 'json',
                type:'delete',
                url: url + '/delete/' + id,
            }).done(function(data){
        
        
                c_obj.remove();
                toastr.success('Item Deleted Successfully.', 'Success Alert', {timeOut: 5000});
                getPageData();
        
        
            });
        
        
        });
        
        
        /* Edit Item */
        $(\"body\").on(\"click\",\".edit-item\",function(){
        
        
            var id = $(this).parent(\"td\").data('id');
            var title = $(this).parent(\"td\").prev(\"td\").prev(\"td\").text();
            var description = $(this).parent(\"td\").prev(\"td\").text();
        
        
            $(\"#edit-item\").find(\"input[name='title']\").val(title);
            $(\"#edit-item\").find(\"textarea[name='description']\").val(description);
            $(\"#edit-item\").find(\"form\").attr(\"action\",url + '/update/' + id);
        
        
        });
        
        
        /* Updated new Item */
        $(\".crud-submit-edit\").click(function(e){
        
        
            e.preventDefault();
        
        
            var form_action = $(\"#edit-item\").find(\"form\").attr(\"action\");
            var title = $(\"#edit-item\").find(\"input[name='title']\").val();
            var description = $(\"#edit-item\").find(\"textarea[name='description']\").val();
        
        
            $.ajax({
                dataType: 'json',
                type:'POST',
                url: form_action,
                data:{title:title, description:description}
            }).done(function(data){
        
        
                getPageData();
                $(\".modal\").modal('hide');
                toastr.success('Item Updated Successfully.', 'Success Alert', {timeOut: 5000});
        
        
            });
        
        
        });";
        //file_put_contents('test.js',$js);
    }

}