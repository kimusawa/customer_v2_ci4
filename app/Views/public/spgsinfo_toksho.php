<?= $this->extend('/layouts/base'); ?>

<?= $this->section('title') ?>特定商取引に基づく表記<?= $this->endSection() ?>

<?= $this->section('content') ?>

<head>
<link rel="stylesheet" href="<?=base_url()?>css/fontawesome/css/all.min.css">
<link rel="stylesheet" href="<?=base_url()?>css/html5reset.css">
<link rel="stylesheet" href="<?=base_url()?>css/tokusyoho.css">
</head>

<body class="main">
<header>
    <div class="header-flex">
        <?php echo form_open('spgsinfo', array('name'=>'return', 'id'=>'return')); ?>
        <div class="header-box1"><button class="button" form="return"><i class="fa fa-caret-left"></i><span>戻る</span></button></div>
        <?=form_close();?>
        <div class="header-box2"><p>特定商取引に基づく表記</p></div>
        <div class="header-box1"></div>
    </div>
</header>

<main>
   
    <div class="main-flex">
        <div class="main-box-l">販売者</div>
        <div class="main-box-r"><?= $misename?></div>
        <div class="main-box-l bgc">電話番号</div>
        <div class="main-box-r bgc"><?= $misetel?></div>
        <div class="main-box-l">システム名</div>
        <div class="main-box-r">SPGS for Web</div>
        <div class="main-box-l bgc">決済方法</div>
        <div class="main-box-r bgc">インターネット</div>
        <div class="main-box-l">支払い方法</div>
        <div class="main-box-r">カード決済サービス（VISA, MasterCard, JCB, AMERICAN EXPRESS, Diners Club） コンビニ決済サービス（ローソン、ミニストップ、ファミリーマート、セブンイレブン、セイコーマート、デイリーヤマザキ） ID決済サービス（PayPay、LINE Pay） 銀行決済サービス（ペイジー）</div>
        <div class="main-box-l bgc">サービスの形態</div>
        <div class="main-box-r bgc">毎月ご利用になられるＬＰガス料金をスマートフォン等を通じて検針票としてお客様にお知らせするサービスです。 また、加盟店様との契約により、オンライン決済のサービスも提供しております。 その場合、お客様はショート・メッセージ・サービスを通じて、上記支払方法に基づき、オンライン決済を行っていただきます。</div>
        <div class="main-box-l">お支払い時期</div>
        <div class="main-box-r">LPガス検針後の当月、もしくは翌月。</div>
        <div class="main-box-l bgc">反映時期</div>
        <div class="main-box-r bgc">すべてのお客様の検針業務が終了後、一斉にショート・メッセージ・サービスにてお知らせします。</div>
        <div class="main-box-l ">利用開始時期</div>
        <div class="main-box-r ">LPガスの「液化石油ガスの保安の確保及び取引の適正化に関する法律」に則り、入居時の供給開始後すぐにご利用可能です。</div>
        <div class="main-box-l bgc">返品の可否と条件</div>
        <div class="main-box-r bgc">商材の性質上、返品は行えません。</div>
        <div class="main-box-l ">解約方法</div>
        <div class="main-box-r ">解約７日前に通知</div>
    </div>

</main>

<footer>
    <div>
    </div>
</footer>
</body>
</html>
