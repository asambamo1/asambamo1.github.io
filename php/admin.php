<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "aravind.arasam@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "1beefe" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'B00D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgMYAhimMIY6IIkFTGEMYQhldAhAFmtlbWV0dHQQQVEn0ujaEAgTAzspNGraytRVkVnTkNyHpg5qHjYxbHZgugWbmwcq/KgIsbgPABt+zCNyiFJEAAAAAElFTkSuQmCC',
			'04FC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YWllDA6YGIImxBjBMZW1gCBBBEhOZwhDKClTNgiQW0MroChJDdl/UUiAIXZmF7L6AVpFWJHVQMdFQVzQxoB2t6HYA3dKK7hawmxsYUNw8UOFHRYjFfQDPa8l+df8QoQAAAABJRU5ErkJggg==',
			'6E8E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7WANEQxlCGUMDkMREpog0MDo6OiCrC2gRaWBtCEQVa0BRB3ZSZNTUsFWhK0OzkNwXgs28VizmYRHD5hZsbh6o8KMixOI+APOzyaR0h3u8AAAAAElFTkSuQmCC',
			'A3B9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDGaY6IImxBoi0sjY6BAQgiYlMYWh0bQh0EEESC2hlAKpzhImBnRS1dFXY0tBVUWFI7oOoc5iKrDc0FGReQAOaeSAxNDsw3RLQiunmgQo/KkIs7gMAIHzNUPSxKvQAAAAASUVORK5CYII=',
			'0B76' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDA6Y6IImxBoi0MjQEBAQgiYlMEWl0aAh0EEASC2gFqmt0dEB2X9TSqWGrlq5MzUJyH1jdFEYU84BijQ4BjA4iaHY4OqCKgdzC2sCAohfs5gYGFDcPVPhREWJxHwDDv8ut95BAWQAAAABJRU5ErkJggg==',
			'1792' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2QMQ6AIAxFy8AN4D5lcK8JLNxAT1EGbqDeAU5pxxIdNaF/e+lPXwr9MQwz5Rc/gz5hghMVswglBCRSzAtbeEU3dKFaJnbKr+39alvuWfnJHkGkgkNXKFMdXSwbpmNkjo24aOajXEwmxQn+92Fe/G7MjslVC1XsCgAAAABJRU5ErkJggg==',
			'69C4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCHRoCkMREprC2MjoENCKLBbSINLo2CLSiiDWAxBimBCC5LzJq6dLUVauiopDcFzKFMdC1AWgist5WBqBextAQFDEWkB3Y3IIihs3NAxV+VIRY3AcA+9LOb4wwK1QAAAAASUVORK5CYII=',
			'8578' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA6Y6IImJTBEBkgEBAUhiAa0gsUAHEVR1IQyNDjB1YCctjZq6dNXSVVOzkNwnMgWoagoDmnkgnYwo5gHtaHR0YESzg7WVtQFVL2sAYwhQDMXNAxV+VIRY3AcAP8/M2CGIhjcAAAAASUVORK5CYII=',
			'410B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpI37pjAEMExhDHVAFgthDGAIZXQIQBJjDGENYHR0dBBBEmMF6mVtCISpAztp2rRVUUtXRYZmIbkvAFUdGIaGQsRE0NyCbgfYfWhuYZjCGorh5oEKP+pBLO4DAJ3ZyKbIiLxMAAAAAElFTkSuQmCC',
			'BF7C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgNEQ11DA6YGIIkFTBEBkQEiyGKtIF6gAwu6ukZHB2T3hUZNDVu1dGUWsvvA6qYwOjCgmxeAKcbowIhhBytQJbJbQgPAYihuHqjwoyLE4j4AJOHMrUlrjggAAAAASUVORK5CYII=',
			'1374' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDAxoCkMRYHURaGRoCGpHFRB0YGh0aAloDUPQytAJFpwQguW9l1qqwVUtXRUUhuQ+sbgqjA5reRocAxtAQNDFHB4YGVHUirawNqGKiIUA3o4kNVPhREWJxHwDqMMsEG7UdPwAAAABJRU5ErkJggg==',
			'159C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGaYGIImxOog0MDo6BIggiYkCxVgbAh1YUPSKhIDEkN23Mmvq0pWZkVnI7mN0YGh0CIGrQ4g1oIuJNDpi2MHaiuGWEMYQdDcPVPhREWJxHwA3SchMIfBIiwAAAABJRU5ErkJggg==',
			'B240' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QgMYQxgaHVqRxQKmsLYytDpMdUAWaxVpBIoEBKCoA+oMdHQQQXJfaNSqpSszM7OmIbkPqG4KayNcHdQ8hgDW0EA0MUYHoIlodrA2MDSiuiU0QDTUAc3NAxV+VIRY3AcAFTjOeINX9d4AAAAASUVORK5CYII=',
			'5EC0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkNEQxlCHVqRxQIaRBoYHQKmOqCJsTYIBAQgiQUGgMQYHUSQ3Bc2bWrY0lUrs6Yhu68VRR1OsYBWTDtEpmC6hTUA080DFX5UhFjcBwDFlcu47/cZOwAAAABJRU5ErkJggg==',
			'4D55' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpI37poiGsIY6hgYgi4WItLI2MDogq2MMEWl0RRNjnQIUm8ro6oDkvmnTpq1MzcyMikJyXwBQnUNDQIMIkt7QUEwxBpB5DYEOaGKtjI4OASjuA7qZIZRhqsNgCD/qQSzuAwC2S8wYo+sUdgAAAABJRU5ErkJggg==',
			'5540' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkNEQxkaHVqRxQIaRBoYWh2mOqCLTXUICEASCwwQCWEIdHQQQXJf2LSpS1dmZmZNQ3ZfK0OjayNcHUIsNBBFLKBVpNGhEdUOkSmsQJWobmENYAxBd/NAhR8VIRb3AQDdRc10Nqt/NAAAAABJRU5ErkJggg==',
			'ADEA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDHVqRxVgDRFpZGximOiCJiUwRaXRtYAgIQBILaAWJMTqIILkvaum0lamhK7OmIbkPTR0YhoaCxUJDcJsHEwO6BV0M5GZHFLGBCj8qQizuAwAx3sxO2ArjdwAAAABJRU5ErkJggg==',
			'2A85' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM2Quw2AMAwFnSIbmH3sIr2RcJMRmMIpsgGwQ5iST2UEJUj4dSc/+WRYb2Pwp3ziFwUENKg4hlMYAjP5PamxRusvDCoWZk7k/Zaljdpy9n5y7JGh6wbqNJlcWDQsab/hGdrZFe+nioUUZvrB/17Mg98GlWbLZAeXcVQAAAAASUVORK5CYII=',
			'B1DA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgMYAlhDGVqRxQKmMAawNjpMdUAWa2UNYG0ICAhAUQfU2xDoIILkvtCoVVFLV0VmTUNyH5o6qHlgsdAQTDFUdSC9jY4oYqFAF7OGMqKIDVT4URFicR8AoZ3LlWBqrtEAAAAASUVORK5CYII=',
			'5321' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkNYQxhCGVqRxQIaRFoZHR2moooxNLo2BIQiiwUGgPQFwPSCnRQ2bVXYqpVZS1Hc1wqFyDa3MjQ6TEGzFyQWgComMgXoFgdUMdYA1hDW0IDQgEEQflSEWNwHAMMJy7cUnkXIAAAAAElFTkSuQmCC',
			'096D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMdkMRYA1hbGR0dHQKQxESmiDS6Njg6iCCJBbSCxBhhYmAnRS1dujR16sqsaUjuC2hlDHR1RNfLANQbiCImMoUFQwybW7C5eaDCj4oQi/sAcAvK0Wktr9kAAAAASUVORK5CYII=',
			'362A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7RAMYQxhCGVqRxQKmsLYyOjpMdUBW2SrSyNoQEBCALDZFBEgGOogguW9l1LSwVSszs6Yhu2+KaCtDKyNMHdw8hymMoSHoYgGo6sBucUAVA7mZNTQQ1bwBCj8qQizuAwBW5MqHDw7mVwAAAABJRU5ErkJggg==',
			'0B0C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7GB1EQximMEwNQBJjDRBpZQhlCBBBEhOZItLo6OjowIIkFtAq0sraEOiA7L6opVPDlq6KzEJ2H5o6mFijK5oYNjuwuQWbmwcq/KgIsbgPAHGqytUKBd+ZAAAAAElFTkSuQmCC',
			'A482' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QMQ6AMAhFf4feAO9Th+6YyOJp6sAN2iN06SmtG1ZHTYSB5EHgBbRbJPwpP/FzAQpBCYZ5RnFzYDaMMsSnJZBhrC72uUTGb6u1NunV+LGS9rnd3hCZJPYOLvugPnEe2ekyMoiT9Qf/ezEf/A7eysw81YX99gAAAABJRU5ErkJggg==',
			'4C58' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpI37pjCGsoY6THVAFgthbXRtYAgIQBJjDBFpcG1gdBBBEmOdItLAOhWuDuykadOmrVqamTU1C8l9AVNAugJQzAsNBYkFopjHMAVkB7oYa6OjowOKXpCbGUIZUN08UOFHPYjFfQCZPsydpj1gTgAAAABJRU5ErkJggg==',
			'B22C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGaYGIIkFTGFtZXR0CBBBFmsVaXRtCHRgQVHH0OgAFEN2X2jUqqWrVmZmIbsPqG4KQyujAwOKeQwBDFPQxYD8AEY0O1gbQKLIbgkNEA11DQ1AcfNAhR8VIRb3AQADLsvqKqyPMwAAAABJRU5ErkJggg==',
			'948D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYWhlCGUMdkMREpjBMZXR0dAhAEgsAqmJtCHQQQRFjdAWpE0Fy37SpS5euCl2ZNQ3JfayuIq1I6iCwVTTUFc08gVaGVnQ7gG5pRXcLNjcPVPhREWJxHwCbrcofqnQn5AAAAABJRU5ErkJggg==',
			'D62B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGUMdkMQCprC2Mjo6OgQgi7WKNLI2BDqIoIoByUCYOrCTopZOC1u1MjM0C8l9Aa2irQytjBjmOUxhRDev0SEATQzkFgdUvSA3s4YGorh5oMKPihCL+wBmR8xWpEv0UAAAAABJRU5ErkJggg==',
			'687A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA1qRxUSmsAL5AVMdkMQCWkQaHRoCAgKQxRqA6hodHUSQ3BcZtTJs1dKVWdOQ3BcCMm8KI0wdRG8r0LwAxtAQNDFHB1R1ILewNqCKgd2MJjZQ4UdFiMV9AGdKy/0wlL23AAAAAElFTkSuQmCC',
			'182C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGaYGIImxOrC2Mjo6BIggiYk6iDS6NgQ6sKDoZW1lAIohu29l1sqwVSszs5DdB1bXyuiAaq9Io8MULGIBjBh2AFWhuiWEMYQ1NADFzQMVflSEWNwHAJjfx7YkzoEUAAAAAElFTkSuQmCC',
			'25D6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGaY6IImJTBFpYG10CAhAEgtoBYo1BDoIIOtuFQkBiaG4b9rUpUtXRaZmIbsvgKHRtSEQxTxGB7CYgwiyWxpEMMSAtraiuyU0lDEE3c0DFX5UhFjcBwA0acxHzzReRgAAAABJRU5ErkJggg==',
			'2D75' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WANEQ1hDA0MDkMREpoi0MjQEOiCrC2gVaXRAE2MAiTU6ujogu2/atJVZS1dGRSG7LwCobgrQXCS9jA5AsQBUMdYGkUZHB6AMslsaRFpZgSYguy80FOjmBoapDoMg/KgIsbgPAO2vy+hJtlQPAAAAAElFTkSuQmCC',
			'B337' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QgNYQxhDGUNDkMQCpoi0sjY6NIggi7UyAEUCUMWmMEBFEe4LjVoVtmrqqpVZSO6DqmtlwDRvChaxAAYMtzg6YHEzithAhR8VIRb3AQD+J843Oi5L9wAAAABJRU5ErkJggg==',
			'DA88' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGaY6IIkFTGEMYXR0CAhAFmtlbWVtCHQQQRETaXREqAM7KWrptJVZoaumZiG5D00dVEw01BWLeRhiUzD1hgaINDqguXmgwo+KEIv7ALx9zohIlOOuAAAAAElFTkSuQmCC',
			'A841' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHVqRxVgDWFsZWh2mIouJTBFpdJjqEIosFtAKVBcI1wt2UtTSlWErM7OWIrsPpI4VzY7QUJFG19CAVlTzgHagqQPbgSEGdnNowCAIPypCLO4DAK4MzcJl6v9eAAAAAElFTkSuQmCC',
			'8DA0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WANEQximMLQii4lMEWllCGWY6oAkFtAq0ujo6BAQgKqu0bUh0EEEyX1Lo6atTF0VmTUNyX1o6uDmuYZiEWsIQLejlbUhAMUtIDcDxVDcPFDhR0WIxX0APyfOGMKDsaYAAAAASUVORK5CYII=',
			'3756' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7RANEQ11DHaY6IIkFTGFodG1gCAhAVtkKEmN0EEAWm8LQyjqV0QHZfSujVk1bmpmZmoXsvikMAQwNgWjmgfQFOoigiLE2sKKJBUwRaWB0dEDRKxoAVBHKgOLmgQo/KkIs7gMASoHLXWIz1fMAAAAASUVORK5CYII=',
			'3603' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7RAMYQximMIQ6IIkFTGFtZQhldAhAVtkq0sjo6NAggiw2RaSBtSGgIQDJfSujpoUtXRW1NAvZfVNEW5HUwc1zBYqIoIk5otmBzS3Y3DxQ4UdFiMV9AFYRzGNIFug8AAAAAElFTkSuQmCC',
			'AE48' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQxkaHaY6IImxBog0MLQ6BAQgiYlMAYpNdXQQQRILaAXyAuHqwE6KWjo1bGVm1tQsJPeB1LE2opoXGgoUCw3ENK8Rix1oegNaMd08UOFHRYjFfQBZ5M1p9sUrCwAAAABJRU5ErkJggg==',
			'FD71' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDA1qRxQIaRID8gKloYo0ODQGhGGKNDjC9YCeFRk1bmbV01VJk94HVTWFAt6PRIQBTzNEBQ6yVtQFdDOjmBobQgEEQflSEWNwHAEAgzn9bWML5AAAAAElFTkSuQmCC',
			'8F20' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WANEQx1CGVqRxUSmiDQwOjpMdUASC2gVaWBtCAgIQFPH0BDoIILkvqVRU8NWrczMmobkPrC6VkaYOrh5DFOwiAUwYNjB6MCA4hbWAKBbQgNQ3DxQ4UdFiMV9AML/y8fKhLOVAAAAAElFTkSuQmCC',
			'AB2D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGUMdkMRYA0RaGR0dHQKQxESmiDS6NgQ6iCCJBbSKtDIgxMBOilo6NWzVysysaUjuA6trZUTRGxoq0ugwhRHdvEaHAAwxoE5GFLcEtIqGsIYGorh5oMKPihCL+wAFhct+mGusYgAAAABJRU5ErkJggg==',
			'5622' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2QsQ2AMAwEP0U2MPuYDRwpoWAEpkgKbxDYgUwJiMYISpDwd/fFn4x2u4w/5RO/FF1EwsyGSfbqeha5MCo+BybDgtDZGL9hWYa2Tm20ftopFMVuQKlw3andOJigWkZ1d2GIZV5c9Cmk+IP/vZgHvw0AzcvXjzwLuwAAAABJRU5ErkJggg==',
			'D8BB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGUMdkMQCprC2sjY6OgQgi7WKNLo2BDqIoIihqAM7KWrpyrCloStDs5Dch6YOj3lYxLC4BZubByr8qAixuA8AWFLN8SM0sUEAAAAASUVORK5CYII=',
			'A555' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDHUMDkMRYA0QaWIEyyOpEpmCKBbSKhLBOZXR1QHJf1NKpS5dmZkZFIbkvoJWh0aEhoEEESW9oKKYY0LxG14ZAB1Qx1lZGR4eAABQxxhCGUIapDoMg/KgIsbgPAHVNzBohxYbnAAAAAElFTkSuQmCC',
			'4067' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpI37pjAEMIQyhoYgi4UwhjA6OjSIIIkxhrC2sjagirFOEWl0BdIBSO6bNm3aytSpq1ZmIbkvAKTO0aEV2d7QUJDegCmobgHZERCAKgZyi6MDFjejig1U+FEPYnEfAPinyx8uoOfpAAAAAElFTkSuQmCC',
			'7B97' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM3QsRGAMAhA0aRwA9wnKewpgkU20ClIwQZxiDilnBU5LfUUutfwD7dfht2f9pU+ojE58pSsCoiPgaG3MjH2VkEGNbR9eZvbkttq+nwAcQnF3h0YSmCs1kAtMqI1vagtMfR2Nnf21f8e3Ju+A+fAy9o5gpKrAAAAAElFTkSuQmCC',
			'4B04' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37poiGMExhaAhAFgsRaWUIZWhEFmMMEWl0dHRoRRZjnSLSytoQMCUAyX3Tpk0NW7oqKioKyX0BYHWBDsh6Q0NFGl0bAkNDUNwCtgPVLVPAbkETw+LmgQo/6kEs7gMAKmrN7q4PueIAAAAASUVORK5CYII=',
			'A3D3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDGUIdkMRYA0RaWRsdHQKQxESmMDS6NgQ0iCCJBbQytLICxQKQ3Be1dFXYUiCZheQ+NHVgGBqK1TwsYphuCWjFdPNAhR8VIRb3AQB1bM5V2lvZnAAAAABJRU5ErkJggg==',
			'5892' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGaY6IIkFNLC2Mjo6BASgiIk0ujYEOoggiQUGsLaygmSQ3Bc2bWXYysyoVVHI7mtlbWUICWhEtoOhVQTID2hFdksAUMyxIWAKspjIFIhbkMVYA0BuZgwNGQThR0WIxX0A1njMjLjlyNAAAAAASUVORK5CYII=',
			'D3AA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgNYQximMLQiiwVMEWllCGWY6oAs1srQ6OjoEBCAKtbK2hDoIILkvqilq8KWrorMmobkPjR1cPNcQwNDQ9DF0NUB3YKuF+RmdLGBCj8qQizuAwBDFs3pxP5XhgAAAABJRU5ErkJggg==',
			'2BA4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM2QsRGAMAhFsXAD3CcW9uQuWGQaUrBBdAMbpzRaEbXUU3737nO8A9bLCPwpr/i11AXIIGQYZlRgSJaRYup7p5aBorZCmazfPI3LGmO0frT3vLO7jcM0sOdgXaSwYlK5yHGjYsxdOLOv/vdgbvw2soHOdkbhwfsAAAAASUVORK5CYII=',
			'E5D0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDGVqRxQIaRBpYGx2mOqCLNQQEBKCKhbA2BDqIILkvNGrq0qWrIrOmIbkPqKfRFaEOj5gIUAzdDtZWdLeEhjCGoLt5oMKPihCL+wDkd85qhKG4OAAAAABJRU5ErkJggg==',
			'BAF8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYAlhDA6Y6IIkFTGEMYW1gCAhAFmtlbWVtYHQQQVEn0uiKUAd2UmjUtJWpoaumZiG5D00d1DzRUFd081pB6vDaAXUzWAzFzQMVflSEWNwHABbzzfLxYfFKAAAAAElFTkSuQmCC',
			'6A6A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGVqRxUSmMIYwOjpMdUASC2hhbWVtcAgIQBZrEGl0bWB0EEFyX2TUtJWpU1dmTUNyX8gUoDpHR5g6iN5W0VDXhsDQEBQxkHmBKOpEgHod0fSyBog0OoQyoogNVPhREWJxHwBm2syHzP6kUQAAAABJRU5ErkJggg==',
			'EF97' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGUNDkMQCGkQaGB0dgCSqGCuYxBQLQHJfaNTUsJWZUSuzkNwH1hUS0MqAphdITkEXY2wICMAQc3R0QHUzUG8oI4rYQIUfFSEW9wEAVsHM2suAVYkAAAAASUVORK5CYII=',
			'1255' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHUMDkMRYHVhbWYEyyOpEHUQaXdHEgLxG16mMrg5I7luZtWrp0szMqCgk9wHVTWFoCGgQQdUbgCnG6MDaEOiAKgZ0iaNDALL7RENEQx1CGaY6DILwoyLE4j4AJsfIVOAMdZgAAAAASUVORK5CYII=',
			'C70B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WENEQx2mMIY6IImJtDI0OoQyOgQgiQU0MjQ6Ojo6iCCLNTC0sjYEwtSBnRS1atW0pasiQ7OQ3AeUD0BSBxVjdACJoZjXyNrAiGaHSCuQh+YW1hCgGJqbByr8qAixuA8ApVfLn9n/VvYAAAAASUVORK5CYII=',
			'60C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhCHVqRxUSmMIYwOgRMdUASC2hhbWVtEAgIQBZrEGl0bWB0EEFyX2TUtJWpq1ZmTUNyX8gUFHUQva3YxDDtwOYWbG4eqPCjIsTiPgDkfsv61ZyeQAAAAABJRU5ErkJggg==',
			'099E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMDkMRYA1hbGR0dHZDViUwRaXRtCEQRC2hFEQM7KWrp0qWZmZGhWUjuC2hlDHQIQdfL0OiAZp7IFJZGRzQxbG7B5uaBCj8qQizuAwAIlMmpFPiUOwAAAABJRU5ErkJggg==',
			'970C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANEQx2mMEwNQBITmcLQ6BDKECCCJBbQytDo6OjowIIq1sraEOiA7L5pU1dNW7oqMgvZfayuDAFI6iCwldEBXUwAaBojmh0iU4CuQHMLK4iH5uaBCj8qQizuAwB9ecqdY9jGwwAAAABJRU5ErkJggg==',
			'B76F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgNEQx1CGUNDkMQCpjA0Ojo6OiCrC2hlaHRtQBObwtDK2sAIEwM7KTRq1bSlU1eGZiG5D6gugBXDPEYH1oZANDHWBgyxKSINjGh6QwNEGhhCGVHEBir8qAixuA8A/i7K/kFPAdgAAAAASUVORK5CYII=',
			'97B6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WANEQ11DGaY6IImJTGFodG10CAhAEgtoBYo1BDoIoIq1sjY6OiC7b9rUVdOWhq5MzUJyH6srQwBQHYp5DK2MDqxA80SQxARaWRvQxUSmiDSwormFNQAohubmgQo/KkIs7gMAZ4HMMQh6gmEAAAAASUVORK5CYII=',
			'C9DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDGUNDkMREWllbWRsdHZDVBTSKNLo2BKKKNaCIgZ0UtWrp0tRVkaFZSO4LaGAMxNTLgGleIwuGGDa3QN2MIjZQ4UdFiMV9AEM6y1GNNILUAAAAAElFTkSuQmCC',
			'71E7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkMZAlhDHUNDkEVbGQNYgbQIihgrptgUBrBYALL7olZFLQ1dtTILyX2MDmB1rcj2AvkgsSnIYiIQsQBksQCwGNAEFDHWUKCbUcQGKvyoCLG4DwDU/MiQAJmdAwAAAABJRU5ErkJggg==',
			'52FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDA0NDkMQCGlhbWRsYHRhQxEQaXdHEAgMYkMXATgqbtmrp0tCVoVnI7mtlmIJuHlAsAMOOVkYHdDERoE50MdYA0VB0twxU+FERYnEfAKD1yPtKPPoiAAAAAElFTkSuQmCC',
			'CEC8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WENEQxlCHaY6IImJtIo0MDoEBAQgiQU0ijSwNgg6iCCLNYDEGGDqwE6KWjU1bOmqVVOzkNyHpg5JjBHVPCx2YHMLNjcPVPhREWJxHwA8Fsw2bQQMJQAAAABJRU5ErkJggg==',
			'8AF7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA0NDkMREpjCGsIJoJLGAVtZWdDGRKSKNriA5JPctjZq2MjV01cosJPdB1bUyoJgnGgoUm4IqBlYXwIBhB6MDqpsxxQYq/KgIsbgPACIFzCqWxWl6AAAAAElFTkSuQmCC',
			'B45B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QgMYWllDHUMdkMQCpjBMZW1gdAhAFmtlCAWJiaCoY3RlnQpXB3ZSaNTSpUszM0OzkNwXMEWklaEhEM08UaCdgajmtQLdgi42haGV0dERRS/IzQyhjChuHqjwoyLE4j4ATsTMU4mH1FAAAAAASUVORK5CYII=',
			'7308' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNZQximMEx1QBZtFWllCGUICEARY2h0dHR0EEEWm8LQytoQAFMHcVPUqrClq6KmZiG5j9EBRR0YsjYwNLo2BKKYB2Rj2BHQgOmWgAYsbh6g8KMixOI+AK4By+gaQ2nIAAAAAElFTkSuQmCC',
			'C0D2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WEMYAlhDGaY6IImJtDKGsDY6BAQgiQU0srayNgQ6iCCLNYg0uoJIJPdFrZq2MhVIRiG5D6qu0QFTbysDhh0BUxiwuAXTzYyhIYMg/KgIsbgPAMKqzWnos7F5AAAAAElFTkSuQmCC',
			'E699' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGaY6IIkFNLC2Mjo6BASgiIk0sjYEOoigijUgiYGdFBo1LWxlZlRUGJL7AhpEWxlCAqai6W10AJuAKubYEIBmB6ZbsLl5oMKPihCL+wD5H8zxIqFrYAAAAABJRU5ErkJggg==',
			'0A37' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhDGUNDkMRYAxhDWBsdGkSQxESmsLYyNASgiAW0ijQ6ANUFILkvaum0lVlTV63MQnIfVF0rA4pe0VCgzikMKHaIgEwLYEBxi0ija6OjA6qbRRodQxlRxAYq/KgIsbgPABO6zPkHQmm/AAAAAElFTkSuQmCC',
			'D67C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDA6YGIIkFTGFtBZIBIshirSKNDA2BDiyoYg0MjY4OyO6LWjotbNXSlVnI7gtoFW1lmMLowIBmnkMAppijAyOqHUC3sDYwoLgF7OYGBhQ3D1T4URFicR8AI8PMwXx4O0cAAAAASUVORK5CYII=',
			'B763' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgNEQx1CGUIdkMQCpjA0Ojo6OgQgi7UyNLo2ODSIoKprZQXRSO4LjVo1benUVUuzkNwHVBfA6ujQgGoeowMrUATFPKBpGGJTRBoY0dwSGgBUgebmgQo/KkIs7gMA61nORXWLkPIAAAAASUVORK5CYII=',
			'A4A5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nM2QsQ3AIAwETcEGZB+noH8KF2EaGm8AI6RhylAakTKR+O/OtnQy9SWFduovfo5JqTqBYR7USMbEsFAHOc+JQV30JUU2fvke6VfOxg8a1BeUYG5FDokyMyiNvcQrA7Cyxhv878O++D2Kj8xmu1OsVgAAAABJRU5ErkJggg==',
			'31A4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RAMYAhimMDQEIIkFTGEMYAhlaEQWY2hlDWB0dGhFEZvCEMAKVB2A5L6VUauilq6KiopCdh9YXaADqnlAsdDA0BB0MaBLUN2CKSYK1IkuNlDhR0WIxX0AxTjMG9nWHogAAAAASUVORK5CYII=',
			'32A2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM3QsQ2AMAwEwKfIBmGfpEj/SKRhGqfIBmYECjIlKR1BCRL+7mVZJ6PdRvCnfOKbOa1Q7MF0VFeRQdrN6kuMMXjbKUoSije+c2vH0bYe41OoE5Yw3ANdZh00dQp9TzFapHcczXNOsuT1B/97MQ++C84IzMwFq5QlAAAAAElFTkSuQmCC',
			'10FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7GB0YAlhDA0NDkMRYHRhDWEEySGKiDqyt6GKMDiKNrggxsJNWZk1bmRq6MjQLyX1o6vCIYbMDi1tCgG5GExuo8KMixOI+ACzfxakRquGgAAAAAElFTkSuQmCC',
			'738F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNZQxhCGUNDkEVbRVoZHR0dUFS2MjS6NgSiik1hQFYHcVPUqrBVoStDs5Dcx+jAgGEeawOmeSJYxAIaMN0S0AB2M6pbBij8qAixuA8AOtTJDNeKGLYAAAAASUVORK5CYII=',
			'9220' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeklEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGVqRxUSmsLYyOjpMdUASC2gVaXRtCAgIQBFjaHRoCHQQQXLftKmrlq5amZk1Dcl9rK4MUxhaGWHqILCVIYBhCqqYAFANUBTFDqBbGoCiKG5hDRANdQ0NQHHzQIUfFSEW9wEAC57LFTKUyFkAAAAASUVORK5CYII=',
			'72B6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDGaY6IIu2srayNjoEBKCIiTS6NgQ6CCCLTWFodG10dEBxX9SqpUtDV6ZmIbmP0YFhCmujI4p5rA0MAaxA80SQxESAKtHFAoAq0d0S0CAa6oru5gEKPypCLO4DAAyRzC3fZfcYAAAAAElFTkSuQmCC',
			'35AB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7RANEQxmmMIY6IIkFTBFpYAhldAhAVtkq0sDo6Ogggiw2RSSEtSEQpg7spJVRU5cuXRUZmoXsvikMja4IdVDzgGKhgajmtYqA1YmguIW1lRVNr2gAI8heFDcPVPhREWJxHwC6j8wVRFszhwAAAABJRU5ErkJggg==',
			'5ACD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYAhhCHUMdkMQCGhhDGB0CHQJQxFhbWRsEHUSQxAIDRBpdGxhhYmAnhU2btjJ11cqsacjua0VRBxUTDUUXCwCrQ7VDZIpIoyOaW1iB9jqguXmgwo+KEIv7ALOHy/JbQdWRAAAAAElFTkSuQmCC',
			'7480' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMZWhlAGFm0lWEqo6PDVAdUsVDWhoCAAGSxKYyujI6ODiLI7otaunRV6MqsaUjuY3QQaUVSB4asDaKhrg2BKGJAdiu6HUB2K7pbAhqwuHmAwo+KEIv7ALIJyy5kfwWbAAAAAElFTkSuQmCC',
			'E3F0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDA1qRxQIaRFpZGximOqCIMTS6NjAEBKCKAdUxOogguS80alXY0tCVWdOQ3IemDsk8bGLodmC6BexmoJmDIfyoCLG4DwCyxsyV532hIgAAAABJRU5ErkJggg==',
			'3AF2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7RAMYAlhDA6Y6IIkFTGEMYW1gCAhAVtnK2srawOgggiw2RaTRFUiLILlvZdS0lamhq1ZFIbsPoq7RAcU80VCgWCuKa1rB6qYwoLgFLBaA6maQGGNoyCAIPypCLO4DAA3yzET7nUNmAAAAAElFTkSuQmCC',
			'29D2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGaY6IImJTGFtZW10CAhAEgtoFWl0bQh0EEHWDRYLaBBBdt+0pUtTV0UBIZL7AhgDgeoake1gdGAA6W1FcUsDC0hsCrKYSAPELchioaEgNzOGhgyC8KMixOI+AFTbzQgRZt3+AAAAAElFTkSuQmCC',
			'CE78' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WENEQ1lDA6Y6IImJtIoAyYCAACSxgEaQWKCDCLJYA5DX6ABTB3ZS1KqpYauWrpqaheQ+sLopDKjmgcQCGFHNA9rB6IAqBnILawOqXrCbGxhQ3DxQ4UdFiMV9AMhFzGsr+V9vAAAAAElFTkSuQmCC',
			'069D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMdkMRYA1hbGR0dHQKQxESmiDSyNgQ6iCCJBbSKNCCJgZ0UtXRa2MrMyKxpSO4LaBVtZQjB0NvogGYeyA5HNDFsbsHm5oEKPypCLO4DAMl7ylvgjyMnAAAAAElFTkSuQmCC',
			'B4D1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgMYWllDGVqRxQKmMExlbXSYiiLWyhDK2hAQiqqO0ZUVJIPkvtCopUuXropaiuy+gCkirUjqoOaJhrpiiDFgqpsCFGt0QBGDujk0YBCEHxUhFvcBAJB+zjITHpoAAAAAAElFTkSuQmCC',
			'4EB8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpI37poiGsoYyTHVAFgsRaWBtdAgIQBJjBIk1BDqIIImxTkFRB3bStGlTw5aGrpqaheS+gCmY5oWGYprHMAWHGJperG4eqPCjHsTiPgBkwMx5l7+zggAAAABJRU5ErkJggg==',
			'3425' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7RAMYWhlCGUMDkMQCpjBMZXR0dEBRCVTF2hCIKjaF0ZWhIdDVAcl9K6OWLl21MjMqCtl9U0RagfobRFDMEw11mIIuBoQBjA4iqG5pZXRgCEB2H8jNrKEBUx0GQfhREWJxHwCg+8pIPkdBCgAAAABJRU5ErkJggg==',
			'857C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA6YGIImJTBEBkgEBIkhiAa0gXqADC6q6EIZGRwdk9y2Nmrp01dKVWcjuE5nC0OgwhdGBAcU8oFgAupgI0DRGNDtYW1kbGFDcwhrAGAIUQ3HzQIUfFSEW9wEAvHTLorpB3roAAAAASUVORK5CYII=',
			'B1F4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgMYAlhDAxoCkMQCpjAGsDYwNKKItbKCxFpR1TGAxKYEILkvNGpV1NLQVVFRSO6DqGN0QDUPLBYagiHG0IDFDhSxUKCL0cUGKvyoCLG4DwB7hMxm6bo8VgAAAABJRU5ErkJggg==',
			'376E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7RANEQx1CGUMDkMQCpjA0Ojo6OqCobGVodG1AE5vC0MrawAgTAztpZdSqaUunrgzNQnbfFIYAVgzzGB1YGwLRxFgb0MUCpog0MKLpFQ0QaWBAc/NAhR8VIRb3AQAlfsmjRmeJRwAAAABJRU5ErkJggg==',
			'8898' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoFWl0bQh0EEFTx9oQAFMHdtLSqJVhKzOjpmYhuQ+kjiEkAMM8BzTzQGKOWOxAdws2Nw9U+FERYnEfAJ7TzJQMmh3gAAAAAElFTkSuQmCC',
			'4437' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37pjC0MoYyhoYgi4UwTGVtdGgQQRJjDGEIZWgIQBFjncLoygBUF4DkvmnTli5dNXXVyiwk9wVMEWkFqmtFtjc0VDQUqHMKuluAqgPQxVgbHR2wuBlVbKDCj3oQi/sAL7rMG0mS9XIAAAAASUVORK5CYII=',
			'4849' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpI37pjCGMDQ6THVAFgthbWVodQgIQBJjDBEBqnJ0EEESY50CVBcIFwM7adq0lWErM7OiwpDcFwBUxwrUjaw3NFSk0TU0oEEExS1AOxodHFDFgHY0oroFq5sHKvyoB7G4DwDCCszX/sj9CgAAAABJRU5ErkJggg==',
			'A807' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQximMIaGIImxBrC2MoQyNIggiYlMEWl0dHRAEQtoZW1lbQgAQoT7opauDFu6KmplFpL7oOpake0NDRVpdG0ImMKAYh7YjgAGNDsYQhkdUMXAbkYRG6jwoyLE4j4ANpbMSY78RxoAAAAASUVORK5CYII='        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>