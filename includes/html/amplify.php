<style>
    body{
        font-family: 'Open Sans',sans-serif;
    }
    .col-xs-12{width: 100%;display: table;float:left}
    .relative{position: relative}
    .absolute{position: absolute}
    .row-fluid{width: 100%;clear: both}
    .row{width: 100%;clear: both}
    .pull-left{float: left}
    .pull-right{float: right}
    /*.head{height: 95px;background-color: #c93c23;padding: 35px 30px 30px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box}*/
    .inner-box{background-color: #fff;padding :25px 0 25px 25px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;width: 670px}
    .header{font-weight: 300;font-size: 18px;color: #585858;line-height: 1;margin-bottom: 10px}
    .small-header{font-size: 12px;line-height: 1;color: #a1a1a1}
    .well{background-color: #fafafa;padding: 20px 38px 20px 25px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;margin-bottom: 20px;float: left}
    .well .control-label{font-size: 16px!important;color:#454545!important;line-height: 1;width: 125px;margin-top: 10px;color: #3d3d3d}
    .well input[type='text']{background-color: #fff;border: 1px solid #e7e6e6;height: 40px;width:400px;}
    .well input[type='text']:focus{outline: none;outline-offset: 0}
    .well .control-group{margin-bottom:15px}
    .warning{color: #993300;font-size: 14px;font-weight: 400;margin: 0;width: 50%;display: inline-block;padding-left: 50px;}
    .btn1{background-color: #e7543c;border-radius: 5px;color: #fff;font-size: 18px;font-weight: 600;padding: 10px 30px;border-style: none;}
    .text-center{ text-align: center}
   
    .label{ font-size: 16px!important;color:#454545!important}
    body {font-family: 'Open Sans', Helvetica, Arial, sans-serif; overflow-x: hidden;font-weight:300}
    root {
        display: block;
    }
    /* Display Style*/
    h1,h2,h3,h4,h5,h6{ margin-bottom: 0;margin-top: 0}
    .block{display:block }
    .inline-block{display:inline-block }

    /* Verticle align Style
    -------------------------------------------------------------- */
    .vertical-align-top{ vertical-align: top }
    .vertical-align-middle{ vertical-align: middle }
    .vertical-align-bottom{ vertical-align: bottom }

    .desc-bx-outer{ width:163px;display: inline-block;float: left;margin-right:50px; }
    /*.desc-bx-outer:first-child{margin-left: 50px}*/
    .desc-bx-outer:last-child{margin-right: 0}
    .desc-bx-outer .top-bx{ width: 155px;height:190px;display: inline-block;float: left;}
    .desc-bx-outer .head-panel{height:60px;background-color: #FF8518;text-align: center;border-radius:3px 3px 0 0;font-size:17px;color:#fff;font-weight:400; padding-top:10px;line-height: 1.2 }
    .desc-bx-outer .text-pos{width:120px;margin-right: auto;margin-left: auto}
    .desc-bx-outer .mid-panel{text-align: center;padding:10px 0;background-color: #dce6e9;border-radius: 0 0 3px 3px }
    .desc-bx-outer i{font-size:46px;color:#2D2D2D;padding: 18px 7px 0}
    .desc-bx-outer .circle-bx{height:80px;width:80px;border-radius:50%;background-color: #F5F5F5;text-align: center;margin:10px auto  }
    .desc-bx-outer .btm-box{height: 170px;overflow: hidden;text-align: center;clear: both}
    .desc-bx-outer .btm-txt{font-size:14px;font-weight:400;color: #474747;text-align: center;line-height:1.2 }
    .desc-bx-outer .eg-txt{font-size:13px;font-weight:400;color: #838383;text-align: center;line-height:1.2;margin-top:10px}
    .plus-sign{color: #494949;font-size: 26px;vertical-align:top;margin:70px 25px 0;display: inline-block }
    .font30{font-size: 30px}
     .desc-bx-outer{ width:160px;display: inline-block;float: none;margin-right:0px; }
    .well {
        min-height: 20px;
        padding: 19px 30px;
        margin-bottom: 20px;
        background-color: #f5f5f5;
        border: 1px solid #e3e3e3;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);margin-top: 30px
    }
</style>
<div class='row-fluid' style="margin-top:30px">
    <div class='pull-left'><img src=<?php echo plugins_url('images/plugin-logo.png', dirname(dirname(__FILE__))); ?>></div>
</div>
<div class='row'>
    <br/>
    <br/>
    <div style="color:#454545;font-size: 18px">
        To enable getamplify, please open an account at <a href="http://getamplify.com" style=" color:#ff7200">www.getamplify.com </a>and
        for your API key and secret.
    </div>
    <form action="" class="well row-fluid">
        <?php $key=get_option('_AMPLIFY_API_KEY', false);
                if(!empty($key)){
                 echo '<div id="success" class="row" style="display:none">';
                }else{
                    echo '<div id="success" class="row" >';
                }
                ?>
        
            <div class='row-fluid control-group pull-left'>
                <div class='pull-left control-label'>API Key</div>
                <div class='pull-left'><input type='text' id="input_apikey" value="<?php echo get_option('_AMPLIFY_API_KEY', false); ?>"/></div>
            </div>
            <div class='row-fluid control-group pull-left'>
                    <div class='pull-left control-label'>Project ID</div>
                    <div class='pull-left'><input type='text' id="input_projectid" value="<?php echo get_option('_AMPLIFY_PROJECT_ID', false);?>"/></div>
             </div>
            <div class='text-center'>
                <input type="button" id="save_btn" name="submit" value="SAVE" class="btn1" style=" margin-right: 15%"/>

            </div>
        </div>
        <?php if(!empty($key)){
                 echo '<div class="text-center" id="change" style=" margin-right: 15%;">';
                }else{
                    echo '<div class="text-center" id="change" style=" margin-right: 15%;display: none">';
                }?>
        
            <a href="javascript:void(0);"  id="newKey" style="font-size: 13px!important;color:#454545!important;font-style: italic;font-weight: 300">Change</a> <span style="font-size: 16px!important;color:#454545!important;font-weight: 400">Succefully Added  <img src="<?php echo plugins_url('images/check.png', dirname(dirname(__FILE__))); ?>"  /></span>
        </div>
        <div class="text-center" id="fail" style=" margin-right: 15%;display: none">
            <span style="font-size: 16px!important;color:#454545!important;font-weight: 400">Not a valid Api Key  </span>
        </div>

    </form>
    <br/>
    <h2 style="color:#494949;font-size: 20px">  A platform that gives you all</h2>
    <p style="color:#454545;font-size: 18px"> Get Amplify is a Marketing Personalization Software and Engagement Platform built in one.</p>
    
    <div class='text-center' style=" margin-bottom: 20px">
        <div class='' style="font-size: 18px;font-weight: 300;color: #454545">You can also mail us at <a href='mailto:support@betaout.com' style="color: #c93a23;text-decoration: none;">support@betaout.com</a></div>
    </div>
    <br/>
    <div class="row">
        <div style=" width: 40%;float:left;display: table">
            <div  class="desc-bx-outer">

                <div class="top-bx" >
                    <div class="head-panel" style=' background-color: #4AAAD4'>
                        <div class="text-pos">
                            Behavioural
                            Database
                        </div>
                    </div>
                    <div class="mid-panel">
                        <div class="text-center" style=' margin: 10px auto'>
                            <a href="http://getamplify.com" target="_blank"><img src="<?php echo plugins_url('images/db-1.png', dirname(dirname(__FILE__))); ?>"  class=" img-responsive"/></a>

                        </div>
                    </div>
                </div>

            </div>
            <span class=' text-center plus-sign' >+</span>
            <div  class="desc-bx-outer">

                <div class="top-bx" >
                    <div class="head-panel" style=' background-color: #4AAAD4'>
                        <div class="text-pos">
                            User
                            Segmentation
                        </div>
                    </div>
                    <div class="mid-panel">
                        <div class="text-center" style=' margin: 10px auto'>
                           <a href="http://getamplify.com" target="_blank"> <img src="<?php echo plugins_url('images/Users.png', dirname(dirname(__FILE__))); ?>"  class=" img-responsive"/></a>

                        </div>
                    </div>
                </div>

            </div>
            <div class='row' style=" margin-bottom: 20px">
                <img src="<?php echo plugins_url('images/mini_bracket.png', dirname(dirname(__FILE__))); ?>"  class=" img-responsive"/>

            </div>
            <div class='text-center'>
                <div class='row font30' style=' font-weight: 300;color:#888888;margin-bottom: 10px;line-height: 1.2'>
                    Marketing Personalization Software
                </div>
                <div class='row font16' style=' font-weight: 400;color:#888888;line-height: 1.2'>
                    A real-time behavioural and personalization software continuously enriching your databases with your users’s actions and profile data.
                </div>
            </div>
        </div>



        <div style=" width: 60%;float:left;display: table">
            <div  class="desc-bx-outer" >

                <div class="top-bx"  >
                    <div class="head-panel" >
                        <div class="text-pos">
                            Engagement
                            Apps
                        </div>
                    </div>
                    <div class="mid-panel">
                        <div class="text-center" style=' margin: 10px auto'>
                            <a href="http://getamplify.com" target="_blank"><img src="<?php echo plugins_url('images/engage-app.png', dirname(dirname(__FILE__))); ?>"  class=" img-responsive"/></a>

                        </div>
                    </div>
                </div>

            </div>
            <span class=' text-center plus-sign' >+</span>
            <div  class="desc-bx-outer">

                <div class="top-bx" >
                    <div class="head-panel" >
                        <div class="text-pos">
                            Communication
                            <div> Apps</div>
                        </div>

                    </div>
                    <div class="mid-panel">
                        <div class="text-center" style=' margin: 10px auto'>
                            <a href="http://getamplify.com" target="_blank"><img src="<?php echo plugins_url('images/com-app.png', dirname(dirname(__FILE__))); ?>"  class=" img-responsive"/></a>

                        </div>
                    </div>
                </div>

            </div>
            <span class=' text-center plus-sign' >+</span>
            <div  class="desc-bx-outer">

                <div class="top-bx" >
                    <div class="head-panel" >
                        <div class="text-pos" >
                            Email
                            <div>Marketing</div>
                        </div>
                    </div>
                    <div class="mid-panel">
                        <div class="circle-bx">
                            <i class="fa fa-envelope" style=" font-size: 44px"></i>
                        </div>
                    </div>
                </div>

            </div>
            <div class='row' style=" margin-bottom: 20px">
                <img src="<?php echo plugins_url('images/large_bracket.png', dirname(dirname(__FILE__))); ?>"  class=" img-responsive"/>

            </div>
            <div class='text-center'>
                <div class='row font30' style=' font-weight: 300;color:#888888;margin-bottom: 10px;line-height: 1.2'>
                    Engagement Platform
                </div>
                <div class='row font16' style=' font-weight: 400;color:#888888;margin-bottom: 10px;line-height: 1.2'>
                    A suite of ready engagement apps all personalised for your users through your Behavioural Database.
                </div>



            </div>
        </div>


    </div>
</div>


<script type="text/javascript" >

    jQuery(document).ready(function($) {
        $('#save_btn').click(function() {
            var apikey=$("#input_apikey").val();
            var projectId=$("#input_projectid").val();
            var data = {
                action: 'verify_key',
                amplifyApiKey:apikey,
                amplifyProjectId:projectId
            };

            $.post(ajaxurl, data, function(response) {
                if(response.responseCode=="200"){
                    $("#success").hide();
                    $("#fail").hide();
                    $("#change").show();
                }else{
                    $("#fail").show();
                }
            },"json");
        });

        $('#newKey').click(function() {
            $("#success").show();
            $("#change").hide();
        });
    });
    
</script>