<script>
    $(document).ready(function () {

           $(document).on('click','li#existing_customer_tab a',function(e){alert('a');
               $('li#existing_customer_tab a').css({color:''});
               $('li#new_customer_tab a').css({color:'#fff'});
           });

            $(document).on('click','li#new_customer_tab a', function(e){alert('b');
                $('li#existing_customer_tab a').css({color:'#fff'});
                $('li#new_customer_tab a').css({color:''});
                $('#new_customer_tab').addClass('active');
                $('#existing_customer_tab').removeClass('active');
                
        
            });

            $(document).on('click','input#quick_estimate_form', function(e){
                    e.preventDefault();
            });

    });
</script>