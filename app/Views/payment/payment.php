<style></style>
<div class="sub_content payment_content t_content">
    <div class="sub_inner">
        <div class="payment_list" id="paymentLoad">
            <!-- [ 교사앱 : 전체반/전체원아 구분 ] -->
            <!-- <div class="top_util">
                <div class="select_form">
                    <div class="select_option option01">
                        <select name="selctClass" id="selctClass">
                            <option value="전체반">전체반</option>
                            <option value="7B-Brown">7B-Brown</option>
                            <option value="7B-IRIS">7B-IRIS</option>
                        </select>
                    </div>
                    <div class="select_option option02">
                        <select name="selctChild" id="selctChild">
                            <option value="전체원아">전체원아</option>
                            <option value="김하랑">김하랑</option>
                            <option value="김하준">김하준</option>
                            <option value="이재영">이재영</option>
                        </select>
                    </div>
                </div>
            </div> -->

            <?php 
                $lmsPaymenturl = "https://fplms.kyowonwiz.com/pay/payMngOnlineMnul/";
                $lmspaymentRecipt = "https://fplms.kyowonwiz.com/payment/receipt/";
            ?>
            <?php foreach( $list as $d => $v ) : ?>
            <div class="list js-load">
                <div class="t_info">
                    <div class="notice_date date"><span><?php echo $v->ISSUE_DT;?></span></div>
                    <div class="pay_title title">청구항목:<span> <?php echo $v->INVOICE_NM?></span></div>
                    <div class="pay_name name">원생명:<span> <?php echo $v->STD_NM?></span></div>
                    <!-- [ 학부모앱 : 결제하기(결제미완료) : a에 .done class 추가 ]  -->
                    <!-- [ 교사앱 : 결제하기 버튼 .pay_btn에 .t_list class 추가 ] -->
                    <?php if ( $is_teacher == false ) : ?>
                        <?php if ( $v->PAY_STAT_CODE == "Y") : // 완납 ?>
                            <div class="pay_btn "><a href="#" data-enc="<?php echo $lmspaymentRecipt; ?><?php echo $v->invoice_enc?>" class="wait">결제내역</a></div>
                        <?php elseif($v->PAY_STAT_CODE == "N") : // 미결재 ?>
                            <div class="pay_btn "><a data-enc="<?php echo $lmsPaymenturl; ?><?php echo $v->invoice_enc?>" class="done" id="done_a<?php echo $d;?>">결제하기</a></div>
                        <?php elseif($v->PAY_STAT_CODE == "P") : // 부분결제 ?>
                            <div class="pay_btn "><a data-enc="<?php echo $lmsPaymenturl; ?><?php echo $v->invoice_enc?>" class="done" id="done_b<?php echo $d;?>">결제하기</a></div>
                        <?php endif; ?>
                    <?php else : ?>
                        <?php if ( $v->PAY_STAT_CODE == "Y") : // 완납 ?>
                            <div class="pay_btn "><a href="#" data-enc="<?php echo $lmspaymentRecipt; ?><?php echo $v->invoice_enc?>" class="wait">결제내역</a></div>
                        <?php elseif($v->PAY_STAT_CODE == "N") : // 미결재 ?>
                            <div class="pay_btn t_list"><a href="#" data-enc="<?php echo $v->invoice_enc?>" class="done">결제하기</a></div>
                        <?php else : // 부분결제 ?>
                            <div class="pay_btn "><a href="#" data-enc="<?php echo $v->invoice_enc?>" class="done">결제하기</a></div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                </div>
            </div>
            <?php endforeach ; ?>
            
        </div>

        <!-- 더보기 -->
        <div id="js-btn-wrap_payList" class="btn-wrap">
            <a href="javascript:;" class="button" id="paymentMoreBtn">더보기<i class="icon_more"></i></a>
        </div>
        <!-- //더보기  -->
    </div>
</div>

<script>
$(window).on('load', function () {
    load('4');
    $("#paymentMoreBtn").on("click", function () {
        load('4', '.btn-wrap');
    })
});
function load(cnt, btn) {
    var payment_list = " .js-load:not(.block)";
    var payment_length = $(payment_list).length;
    var payment_total_cnt;
    if (cnt < payment_length) {
        payment_total_cnt = cnt;
    } else {
        payment_total_cnt = payment_length;
        $('.btn-wrap').hide();
    }
    $(payment_list + ":lt(" + payment_total_cnt + ")").addClass("block");
}
</script>