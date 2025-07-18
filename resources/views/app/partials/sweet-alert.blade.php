
@if (session()->has('flash_message'))
     <script type="text/javascript">
        swal({   
          title: "{{ session('flash_message.title') }}",   
          text: "{{ session('flash_message.message') }}",   
          type: "{{ session('flash_message.level') }}",
          timer: 1500,
          showConfirmButton: false
        });
    </script>
@endif

@if (session()->has('flash_message_confirm'))
     <script type="text/javascript">
        swal({   
          title: "{{ session('flash_message_confirm.title') }}",   
          text: "{{ session('flash_message_confirm.message') }}",   
          type: "{{ session('flash_message_confirm.level') }}",
          confirmButtonText: "{{ session('flash_message_confirm.buttonText') }}"
        });
    </script>
@endif