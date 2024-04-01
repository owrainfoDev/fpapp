<?php echo $this->include('./layout/common/dflipCustom');?>

<div class="sub_content album_content">
    <div class="sub_inner">
        <div class="search_form">
            <!-- [ 교사앱 : 검색/셀렉트박스 ] -->
            <form name="t_form" id="t_form" class="t_form">
                
                <?php if ($is_teacher == true):?>
                <div class="select_form t_select">
                    
                    <div class="select_option option01">
                        <select name="selectClass" id="selectClass"> 
                            <option value="" >전체반</option>                                
                            <?php foreach ($classList as $class): ?>
                                <option value="<?php echo $class->CLASS_CD?>" <?php echo ( $class->CLASS_CD == $search['selectClass'] ? "selected" : "" ) ?>><?php echo $class->CLASS_NM?></option>
                            <?php endforeach ;?>
                        </select>
                    </div>

                    <div class="select_option option02">
                        <select name="selectChild" id="selectChild">
                            <option value="">전체 원아</option>
                        </select>
                    </div>
                </div>
                <?php endif; ?>
                <div class="search">
                    <input type="search" id="noteSearch" name="noteSearch" placeholder="검색어 입력" value="<?php echo $search['noteSearch']?>">
                        <button type="button" class="search_btn" id="SearchBtn">
                            <span></span>
                        </button>
                </div>
            </form>
            <!-- [ 학부모앱 : 검색/셀렉트박스 ] -->
            <!-- <form action="" class="p_form">
                <div class="search">
                    <input type="search" id="noteSearch" name="noteSearch" placeholder="검색어 입력">
                </div>
                <div class="select_form">
                    <div class="select_option option01">
                        <select name="selectClass" id="selectClass">                             
                            <option value="7B-IRIS">7B-IRIS</option>
                            <option value="7B-Brown" selected="">7B-Brown</option>
                            <option value="7B-IRIS">7B-IRIS</option>
                            <option value="7B-Brown">7B-Brown</option>
                        </select>
                    </div>
                </div>
            </form> -->
        </div>

        <div class="album_list" id="albumLoad">
            
        </div>
        <!-- //앨범 목록 -->
    </div>
    <!-- 더보기 -->
    <div id="js-btn-wrap_album" class="btn-wrap">
        <a href="javascript:;" class="button" id="more">더보기<i class="icon_more"></i></a>
    </div>
        <!-- //더보기  -->
    <!-- [ 교사앱 : 알림장 쓰기 ] -->
    <?php if ($is_teacher == true): ?>
    <a href="/album/write" class="request_writer write_btn" style="background: #fff;"><i></i></a>
    <?php endif; ?>
</div>

<script type="text/javascript">
var searchselectClass = "<?php echo $search['selectClass']?>";
var searchselectChild = "<?php echo $search['selectChild']?>";
var searchnoteSearch = "<?php echo $search['noteSearch']?>";
var page = 1;
var total = 0;
var totallist;
var List = {
    init : function(){
        this.getStudentsList();
        this.getMore();
    },
    getStudentsList : function (){
        <?php if ($is_teacher == true) : ?>
            var forms = {
                class_cd : $('#selectClass').val() ,
            }
            fetch("/api/ajax/getstudentsFromClass", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(forms) })
            .then((response) => response.json())
            .then((data) => {
                $('#selectChild').empty();
                $('#selectChild').append($("<option value=''>전체 원아</option>"));
                $.each(data, function (index , item){
                    $('#selectChild').append($("<option value='" + item.STD_ID + "' " + ( searchselectChild == item.STD_ID ? "SELECTED" : "" )  +">"+item.STD_NM+"</option>"));
                })
            });
        <?php endif; ?>
    },
    getMore : function(){
        var forms = {
            searchselectClass : $('#selectClass').val() ,
            searchselectChild : $('#selectChild').val() ,
            searchnoteSearch : $('#noteSearch').val()
        }
        fetch("/album/ajax/listMore?more=" + page , { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(forms) })
        .then((response) => response.json())
        .then((data) => {
            $('#albumLoad').append(data.html);
            totallist += data.html;
            // history.replaceState({list:totallist, page:page},'Page '+ page, '/album##');
            if (data.sql.total_page <= page) $('#js-btn-wrap_album').hide();
            page++;
        });
    },
    emptyList : function(){
        $('#albumLoad').empty();
    },
    search : function(){
        
    }
}

List.init();

$(document).on('change' , '#selectClass' , function(){
    page = 1;
    List.getStudentsList();
    List.emptyList();
    List.getMore();
});

$(document).on('change' , '#selectChild' , function(){
    page = 1;
    List.emptyList();
    List.getMore();
})

$(document).on('click' , '#SearchBtn' , function(){
    page = 1;
    List.emptyList();
    List.getMore();
})

$('input[type="search"]').keydown(function(event) {
    if (event.keyCode === 13) {
        page = 1;
        List.emptyList();
        List.getMore();
        event.preventDefault();
    };
});

$(document).on('click' , '#more' , function(){
    List.getMore();
})

</script>