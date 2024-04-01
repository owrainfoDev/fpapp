<?php 
$lmsPaymenturl = "http://fplms.kyowonwiz.com/pay/payMngOnlineMnul/";
$lmspaymentRecipt = "https://fplms.kyowonwiz.com/payment/receipt/";
?>

<?php foreach( $list as $d => $v ) : ?>
<div class="list js-load block">
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
            <?php else : // 부분결제 ?>
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