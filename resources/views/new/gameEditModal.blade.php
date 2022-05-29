<form class="formEditForm">
    <div class="row">
        <div class="col-6">
            <label for="name" class="form-label">Name</label>
            <input required name="name" type="text" class="form-control" id="name" placeholder="Name" value="{{ $form->name }}">
        </div>
        <div class="col-6">
            <label for="title" class="form-label">Title</label>
            <input required name="title" type="text" class="form-control" id="title" placeholder="Title" value="{{ $form->title }}">
        </div>
        <div class="col-6 mt-2">
            <label for="balance" class="form-label">Balance</label>
            <input required name="balance" type="number" class="form-control" id="balance" placeholder="Balance" value="{{ $form->balance }}">
        </div>
        <div class="col-6 mt-2">
            <label for="status" class="form-label">Status</label>
            <select required name="status" id="" class="form-control status">
                <option {{ ($form->status == 'active')?'selected':'' }} value="active">Active</option>
                <option {{ ($form->status == 'inactive')?'selected':'' }} value="inactive">Inactive</option>
            </select>
        </div>
        <div class="col-12 mt-2">
            <button data-id="{{$form->id}}" class="formSubmitBtn">Submit</button>
        </div>
    </div>
</form>
<script>
      $('.formSubmitBtn').on('click',function(e) {
          e.preventDefault();
            var datastring = $(".formEditForm").serialize();
            var cid = $(this).data('id');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            var dataSend = {
                'name' : $('input[name="name"]').val(),
                'title' : $('input[name="title"]').val(),
                'balance' : $('input[name="balance"]').val(),
                'status' : $('.status').val(),
            };
                        $.ajax({
                        type: 'post',
                        url: "/games/update/"+cid,
                        data: {
                            "data": dataSend,
                        },
                        dataType: 'json',
                        success: function (data) {
                            console.log(data);
                            $('.editFormModal').modal('hide');
                            $('.td-name-'+cid).text((data.name));
                            $('.td-title-'+cid).text(data.title);
                            $('.td-balance-'+cid).text(data.balance);
                            $('.td-status-'+cid).text((data.status));

                            toastr.success('Success',"Form Updated");
                            
                        },
                        error: function (data) {
                            console.log(data);
                            toastr.error('Error',data.responseText);
                        }
                    });
            console.log(datastring);
        });
</script>