<!-- js -->
<script type="text/javascript" src="/resources/js/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="/resources/js/remodal.js"></script>
<script type="text/javascript" src="/resources/js/common.ui.js"></script>
<script type="text/javascript" src="/resources/js/common.js"></script>
<script type="text/javascript" src="/resources/js/tempSave.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script type="text/javascript">
    $.fn.serializeObject = function()
    {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
    };

    $(document).ready(function(){
        $(document).on('click', '.maingo' , function(){
            location.href="<?php echo base_url(); ?>";
        })
    })
</script>