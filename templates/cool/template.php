<?php
// Templates must specify all the forms of the newsletter that can be generated in an array called $types
$types = array('web', 'email', 'print');

$break = <<<BREAK

<!--break-->
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	    <td height="10"></td>
	</tr>
    </table>
<!--/break-->

BREAK;


$bigBreak = <<<BIGBREAK

<!--break-->
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	    <td height="20"></td>
	</tr>
    </table>
<!--/break-->

BIGBREAK;


$hr = <<<HOZRULE

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
	<td valign="middle" height="40"> <img src="<!--TEMPLATE IMAGE PATH-->line2.jpg" height="12" width="309" /></td>
    </tr>
</table>

HOZRULE;

/*
 * $template[$newsletterFormat][$blockType][$section][$item]
 * 'between' blockType adds aother dimension to the array
 * 
 * $newsletterFormat
 * 'web'
 * 'email'
 * 'print'
 * etc
 * 
 * $blockType
 * 'begin'
 * 'end'
 * 'whole'
 * 'between'
 * 
 * $seciton
 * 'header'
 * 'main'
 * 'secondary'
 * 'footer'
 * 
 * $item
 * 'container'
 * 'article'
 * 'articleNoTitle'
 * 'para'
 * 'list'
 * 'image'
 * 'mugshot'
 * 'logo'
 */

$template['web']['begin']['header']['container'] = <<<WEBHEADERBEGIN
            <!--header-->
            <table style="background-image: url(<!--TEMPLATE IMAGE PATH-->header-lines.jpg); background-position: bottom; background-repeat: repeat-x;" bgcolor="#1d3952" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <table id="top" width="578" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td valign="top" height="60">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td height="10"></td>
                                        </tr>
                                    </table>
WEBHEADERBEGIN;

$template['web']['end']['header']['container'] = <<<WEBHEADEREND
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!--/header-->
WEBHEADEREND;

$template['web']['whole']['header']['text'] = '<!--header text--><p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #4484b5; margin: 0; padding: 0;"><!--CONTENT--></p><!--/header text-->';
$template['web']['between']['header']['text-text'] = "\n";


$template['web']['whole']['header']['link'] = '<a style="color: #62b6ee;" href="<!--LINK URL-->"><!--LINK TEXT--></a>';

$template['web']['begin']['footer']['container'] = <<<WEBFOOTERBEGIN
            <!--footer-->
            <table style="background-image: url(<!--TEMPLATE IMAGE PATH-->footer-lines.jpg); background-position: top; background-repeat: repeat-x;" bgcolor="#1c293b" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <table width="578" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td valign="top" align="center" height="60">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td height="15"></td>
                                        </tr>
                                    </table>
WEBFOOTERBEGIN;

$template['web']['end']['footer']['container'] = <<<WEBFOOTEREND
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!--/footer-->
WEBFOOTEREND;

$template['web']['whole']['footer']['text'] = '<!--footer text--><p style="font-family: Helvetica, Arial, sans-serif; font-size: 13px; margin: 0px; padding: 0px; color: #30648d; text-shadow: 1px 1px 1px #000;"><!--CONTENT--></p><!--/footer text-->';
$template['web']['between']['footer']['text-text'] = "\n";

$template['web']['whole']['footer']['link'] = $template['web']['whole']['header']['link'];

$template['web']['begin']['newsletter']['container'] = <<<WEBNEWSLETTERBEGIN
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><!--TITLE_ONSET--> <!--TITLE_CODA--> - Issue <!--NUM--></title>
<!--[if gte mso 9]>
<style type="text/css">
.body { background: #234865 url('<!--TEMPLATE IMAGE PATH-->body-bg.jpg'); }	     
.case { background: none; }
</style>
<![endif]-->
</head>
<body class="body" style="margin: 0; padding: 0; background-color: #234865; background-image: url(<!--TEMPLATE IMAGE PATH-->body-bg.jpg); background-repeat: repeat;" marginheight="0" topmargin="0" marginwidth="0" leftmargin="0">
<!--100% body table-->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="case" bgcolor="#234865" style="background-color: #234865; background-image: url(<!--TEMPLATE IMAGE PATH-->body-bg.jpg); background-repeat: repeat;">
            <!--[if gte mso 9]>
<td style="background: none;">
<![endif]-->
            <!--HEADER-->
            <!--intro-->
            <table width="578" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td valign="top" height="20"></td>
                </tr>
                <tr>
                    <td background="<!--TEMPLATE IMAGE PATH-->header-bg.jpg" bgcolor="#c6d2db" valign="top" height="79">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr><td>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td height="22" width="68%"></td>
                                <td height="20" width="32%"></td>
                            </tr>
                            <tr>
                                <td valign="top">
                                    <table width="82%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="center" valign="bottom" width="7%">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td width="15"></td>
                                                        <td width="10"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td valign="middle" width="93%">
                                                <h1 style="font-family: Helvetica, Arial, sans-serif; font-size: 24px; margin: 0px; padding: 0px; text-shadow: 1px 1px 1px #FFFFFF; color: #244a67"><!--TITLE_ONSET--><span style=" font-weight: normal;"><!--TITLE_CODA--></span></h1>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td valign="top">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td height="10"></td>
                                        </tr>
                                    </table>
                                    <p style=" font-family:Helvetica, Arial, sans-serif; font-weight: bold; font-size: 12px; margin: 0px; padding: 0px; text-shadow: 1px 1px 1px #FFFFFF; color: #336791">
                                        Issue <!--NUM--> - <!--DATE-->
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <!--MUGSHOT-->
                    </tr>
                    </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" height="20"></td>
                </tr>
            </table>
            <!--/intro-->
            <!--content section-->
            <table width="576" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
WEBNEWSLETTERBEGIN;

$template['web']['end']['newsletter']['container'] = <<<WEBNEWSLETTEREND
</tr>
            </table>
            <!--break-->
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td height="25"></td>
                </tr>
            </table>
            <!--/break-->
            <!--FOOTER-->
        </td>
    </tr>
</table>
<!--/100% body table-->
</body>
</html>
WEBNEWSLETTEREND;

$template['web']['whole']['newsletter']['mugshot'] = <<<WEBMUGSHOT
<td>
                         <table>
                           <tr><td height="5"></td></tr>
                           <tr>
                             <td><a href="<!--CONTENT-->"><img src="<!--CONTENT-->" alt="Me or Us" width="80" /></a></td>
                           </tr>
                         </table>
                      </td>
WEBMUGSHOT;

$template['web']['begin']['main']['container'] = <<<WEBMAINBEGIN
                    <!--main section-->
                    <td width="350" valign="top">
                        <table width="100%" border="0" cellpadding="20" cellspacing="0">
                            <tr>
                                <td valign="top" bgcolor="#1c3851" background="<!--TEMPLATE IMAGE PATH-->content-bg.jpg" style="border: solid 1px #193044; border-radius: 8px; -moz-border-radius: 8px; -webkit-border-radius: 8px; -khtml-border-radius: 8px;">
WEBMAINBEGIN;

$template['web']['end']['main']['container'] = <<<WEBMAINEND
                                </td>
                            </tr>
                        </table>
                        <!--break-->
                    </td>
                    <!--/main section-->
WEBMAINEND;

$template['web']['begin']['secondary']['container'] = '<!--secondary section--><td valign="top">';
$template['web']['end']['secondary']['container'] ='</td><!--/secondary section-->';

// include title in main article beginning
$template['web']['begin']['main']['article'] = '<!--main article--><h2 style="font-family: Helvetica, Arial, sans-serif; font-size: 18px; margin: 0px; padding: 0px; text-shadow: 1px 1px 1px #333333; color: #62b6ee;"><!--ARTICLE TITLE--></h2>';

// an article may also not have a title
$template['web']['begin']['main']['articleNoTitle'] = '<!--main article-->';
$template['web']['end']['main']['article'] = "\n<!--/main article-->\n";
$template['web']['between']['main']['article-article'] = $break . $hr . $break;

// include title in secondary article beginning
$template['web']['begin']['secondary']['article'] = <<<SECONDARYARTICLEBEGIN

                        <!--secondary article-->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="21">&nbsp;</td>
                                            <td>
                                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td height="17" width="204" valign="top" bgcolor="#1c3851" background="<!--TEMPLATE IMAGE PATH-->content-bg.jpg" style="border: solid 1px #193044; 
					-webkit-border-top-left-radius: 8px;
	                -khtml-border-radius-topleft: 8px;	
	                -moz-border-radius-topleft: 8px;
	                border-top-left-radius: 8px;
					-webkit-border-top-right-radius: 8px;
	                -khtml-border-radius-topright: 8px;	
	                -moz-border-radius-topright: 8px;
	                border-top-right-radius: 8px;
				    border-bottom: none;"> </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td width="19">&nbsp;</td>
                                        </tr>
                                    </table>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td height="38" align="center">
                                                <table width="202" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td valign="top" style="background-image: url(<!--TEMPLATE IMAGE PATH-->ribbon.jpg); background-repeat: no-repeat; background-position: center;" bgcolor="#2b7ab7" height="38">
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td width="10"></td>
                                                                    <td height="6"></td>
                                                                    <td height="6"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                    <td>
                                                                        <p style="font-family: Helvetica, Arial, sans-serif; font-size: 13px; margin-top: 0px; margin-bottom: 0px; padding: 0px; color: #1f2d41; font-size: 16px; font-weight: bold; text-shadow: 0px 1px 1px #ffffff"><!--ARTICLE TITLE--></p>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="20">&nbsp;</td>
                                            <td>
                                                <table width="100%" border="0" cellpadding="20" cellspacing="0">
                                                    <tr>
                                                        <td width="204" valign="top" bgcolor="#1c3851" background="<!--TEMPLATE IMAGE PATH-->content-bg.jpg" style="border: solid 1px #193044; 
					-webkit-border-bottom-left-radius: 8px;
	                -khtml-border-radius-bottomleft: 8px;	
	                -moz-border-radius-bottomleft: 8px;
	                border-bottom-left-radius: 8px;
					-webkit-border-bottom-right-radius: 8px;
	                -khtml-border-radius-bottomright: 8px;	
	                -moz-border-radius-bottomright: 8px;
	                border-bottom-right-radius: 8px;
				    border-top: none;">
SECONDARYARTICLEBEGIN;

// and article may not have a title
// TODO: adjust this block of HTML so it looks right
$template['web']['begin']['secondary']['articleNoTitle'] = <<<SECONDARYARTICLENOTITLEBEGIN

                        <!--secondary article-->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="21">&nbsp;</td>
                                            <td>
                                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td height="17" width="204" valign="top" bgcolor="#1c3851" background="<!--TEMPLATE IMAGE PATH-->content-bg.jpg" style="border: solid 1px #193044; 
					-webkit-border-top-left-radius: 8px;
	                -khtml-border-radius-topleft: 8px;	
	                -moz-border-radius-topleft: 8px;
	                border-top-left-radius: 8px;
					-webkit-border-top-right-radius: 8px;
	                -khtml-border-radius-topright: 8px;	
	                -moz-border-radius-topright: 8px;
	                border-top-right-radius: 8px;
				    border-bottom: none;"> </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td width="19">&nbsp;</td>
                                        </tr>
                                    </table>
                                    
SECONDARYARTICLENOTITLEBEGIN;

$template['web']['end']['secondary']['article'] = <<<SECONDARYARTICLEEND
                                    
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td width="19">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <!--/secondary article-->
                        
SECONDARYARTICLEEND;

$template['web']['between']['secondary']['article-article'] = $bigBreak;


$template['web']['whole']['main']['para'] = '<!--para--><p style="font-family: Helvetica, Arial, sans-serif; font-size: 13px; margin-top: 0px; margin-bottom: 0px; padding: 0px; color: #e7eff6;"><!--CONTENT--></p><!--/para-->';

$template['web']['whole']['main']['list'] = '<!--list--><p style="font-family: Helvetica, Arial, sans-serif; list-style: none; font-size: 13px; margin-top: 0px; margin-bottom: 5px; padding: 0px; color: #e7eff6"><img src="<!--TEMPLATE IMAGE PATH-->li.png" height="14" width="13" /><!--CONTENT--></p><!--/list-->';

$template['web']['whole']['main']['image'] = '<!--image--><a href="<!--IMAGE PATH--><!--CONTENT-->"><img style="-moz-box-shadow: 2px 2px 4px #000; -webkit-box-shadow: 2px 2px 4px #000; box-shadow: 2px 2px 4px #000; margin: 0; padding: 0; display: block;" src="<!--IMAGE PATH--><!--CONTENT-->" width="309" /></a><!--/image-->';

//$template['web']['between']['main']['list-list'] = $smallBreak;

// this is a shortcut for when you have the same thing between many or all of the item combinations
// it is used when no specific between is assigned
$template['web']['between']['main']['items'] = $break;

$template['web']['whole']['main']['link'] = $template['web']['whole']['header']['link'];


$template['web']['whole']['secondary']['para'] = $template['web']['whole']['main']['para'];

$template['web']['whole']['secondary']['list'] = $template['web']['whole']['main']['list'];

$template['web']['whole']['secondary']['image'] = '<!--image--><a href="<!--IMAGE PATH--><!--CONTENT-->"><img style="-moz-box-shadow: 2px 2px 4px #000; -webkit-box-shadow: 2px 2px 4px #000; box-shadow: 2px 2px 4px #000; margin: 0; padding: 0; display: block;" src="<!--IMAGE PATH--><!--CONTENT-->" width="204" /></a><!--/image-->';

$template['web']['between']['secondary']['items'] = "\n";

$template['web']['whole']['secondary']['link'] = $template['web']['whole']['header']['link'];


// The template for print and email is the same as that for web unless otherwise specified below this line
$template['print'] = $template['email'] = $template['web'];

// must use full path for images in email
$template['email']['begin']['header']['container'] = str_replace('<!--TEMPLATE IMAGE PATH-->', '<!--FULL TEMPLATE IMAGE PATH-->', $template['web']['begin']['header']['container']);

$template['email']['begin']['footer']['container'] = str_replace('<!--TEMPLATE IMAGE PATH-->', '<!--FULL TEMPLATE IMAGE PATH-->', $template['web']['begin']['footer']['container']);

$template['email']['begin']['newsletter']['container'] = str_replace('<!--TEMPLATE IMAGE PATH-->', '<!--FULL TEMPLATE IMAGE PATH-->', $template['web']['begin']['newsletter']['container']);

$template['email']['begin']['main']['container'] = str_replace('<!--TEMPLATE IMAGE PATH-->', '<!--FULL TEMPLATE IMAGE PATH-->', $template['web']['begin']['main']['container']);

$template['email']['begin']['secondary']['article'] = str_replace('<!--TEMPLATE IMAGE PATH-->', '<!--FULL TEMPLATE IMAGE PATH-->', $template['web']['begin']['secondary']['article']);

$template['email']['begin']['secondary']['articleNoTitle'] = str_replace('<!--TEMPLATE IMAGE PATH-->', '<!--FULL TEMPLATE IMAGE PATH-->', $template['web']['begin']['secondary']['articleNoTitle']);

$template['email']['whole']['main']['list'] = str_replace('<!--TEMPLATE IMAGE PATH-->', '<!--FULL TEMPLATE IMAGE PATH-->', $template['web']['whole']['main']['list']);

$template['email']['whole']['secondary']['list'] = str_replace('<!--TEMPLATE IMAGE PATH-->', '<!--FULL TEMPLATE IMAGE PATH-->', $template['web']['whole']['list']['list']);

$template['email']['whole']['main']['image'] = str_replace('<!--IMAGE PATH-->', '<!--FULL IMAGE PATH-->', $template['web']['whole']['main']['image']);

$template['email']['whole']['secondary']['image'] = str_replace('<!--IMAGE PATH-->', '<!--FULL IMAGE PATH-->', $template['web']['whole']['secondary']['image']);


$template['print']['begin']['header']['container'] = '';
$template['print']['end']['header']['container'] = '';

$template['print']['whole']['header']['text'] = '<tr><td><!--CONTENT--></td></tr>';

$template['print']['begin']['footer']['container'] = <<<PRINTFOOTERBEGIN
            <!--footer-->
            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <table width="578" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td valign="top" align="center" height="60">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td height="15"></td>
                                        </tr>
                                    </table>
PRINTFOOTERBEGIN;

$template['print']['whole']['footer']['text'] = '<p style="font-family: Helvetica, Arial, sans-serif; font-size: 13px; margin: 0px; padding: 0px"><!--CONTENT--></p>';

$template['print']['begin']['newsletter']['container'] = <<<PRINTNEWSLETTERBEGIN
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><!--TITLE_ONSET--> <!--TITLE_CODA--> - Issue <!--NUM--></title>
<!--[if gte mso 9]>
<style type="text/css">
.body {background: none;}	     
.case {background: none;}
</style>
<![endif]-->
</head>
<body class="body" style="margin: 0; padding: 0;" marginheight="0" topmargin="0" marginwidth="0" leftmargin="0">
<!--100% body table-->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<!--HEADER-->
    <tr>
        <td class="case" >
            <!--[if gte mso 9]>
<td style="background: none;">
<![endif]-->

            <!--intro-->
            <table width="578" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td valign="top" height="20"></td>
                </tr>
                <tr>
                    <td valign="top" height="79">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                       <tr><td>
                            <tr>
                                <td height="22" width="68%"></td>
                                <td height="20" width="32%"></td>
                            </tr>
                            <tr>
                                <td valign="top">
                                    <table width="82%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="center" valign="bottom" width="7%">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td width="15"></td>
                                                        <td width="10"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td valign="middle" width="93%">
                                                <h1 style="font-family: Helvetica, Arial, sans-serif; font-size: 24px; margin: 0px; padding: 0px; color: #000000">Anderson<span style=" font-weight: normal;">Report</span></h1>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td valign="top">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td height="10"></td>
                                        </tr>
                                    </table>
                                    <p style=" font-family:Helvetica, Arial, sans-serif; font-weight: bold; font-size: 12px; margin: 0px; padding: 0px; color: #111111">
                                        Issue <!--NUM--> - <!--DATE-->
                                    </p>
                                </td>
                            </tr>
                        </table>
                       </td>
                       </tr>
                     </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" height="20"></td>
                </tr>
            </table>
            <!--/intro-->
            <!--content section-->
            <table width="576" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
PRINTNEWSLETTERBEGIN;

$template['print']['end']['newsletter']['container'] = <<<PRINTNEWSLETTEREND
                </tr>
            </table>
            <!--break-->
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td height="10"></td>
                </tr>
            </table>
            <!--/break-->
            <!--FOOTER-->
        </td>
    </tr>
</table>
<!--/100% body table-->
</body>
</html>
PRINTNEWSLETTEREND;

$template['print']['begin']['main']['container'] = <<<PRINTMAINBEGIN
                    <td width="350" valign="top">
                        <table width="100%" border="0" cellpadding="20" cellspacing="0">
                            <tr>
                                <td valign="top" style="border: solid 1px #444444; border-radius: 8px; -moz-border-radius: 8px; -webkit-border-radius: 8px; -khtml-border-radius: 8px;">
PRINTMAINBEGIN;

// include title in secondary article beginning
$template['print']['begin']['secondary']['article'] = <<<PRINTSECONDARYARTICLEBEGIN
<!--section-->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="20">&nbsp;</td>
                                            <td>
                                                <table width="100%" border="0" cellpadding="20" cellspacing="0">
                                                    <tr>
                                                        <td width="204" valign="top"  style="border: solid 1px #222222;  
					-webkit-border-top-left-radius: 8px;
	                -khtml-border-radius-topleft: 8px;	
	                -moz-border-radius-topleft: 8px;
	                border-top-left-radius: 8px;
					-webkit-border-top-right-radius: 8px;
	                -khtml-border-radius-topright: 8px;	
	                -moz-border-radius-topright: 8px;
	                border-top-right-radius: 8px;
					-webkit-border-bottom-left-radius: 8px;
	                -khtml-border-radius-bottomleft: 8px;	
	                -moz-border-radius-bottomleft: 8px;
	                border-bottom-left-radius: 8px;
					-webkit-border-bottom-right-radius: 8px;
	                -khtml-border-radius-bottomright: 8px;	
	                -moz-border-radius-bottomright: 8px;
	                border-bottom-right-radius: 8px;">
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                    <td>
                                                                        <p style="font-family: Helvetica, Arial, sans-serif; font-size: 13px; margin-top: 0px; margin-bottom: 0px; padding: 0px; color: #1f2d41; font-size: 14px; font-weight: bold;"><!--TITLE--></p>
                                                                    </td>
                                                                    <td>&nbsp;<img style="margin: 0; padding: 0; display: block;" src="images/i.png" width="18" height="20" alt="info"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="10"></td>
                                                                    <td height="6"></td>
                                                                    <td height="6"></td>
                                                                </tr>
                                                            </table>
PRINTSECONDARYARTICLEBEGIN;

// and article may not have a title
// TODO: check this looks right
$template['web']['begin']['secondary']['srticleNoTitle'] = <<<SECONDARYARTICLENOTITLEBEGINPRINT

<!--section-->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="20">&nbsp;</td>
                                            <td>
                                                <table width="100%" border="0" cellpadding="20" cellspacing="0">
                                                    <tr>
                                                        <td width="204" valign="top"  style="border: solid 1px #222222;  
					-webkit-border-top-left-radius: 8px;
	                -khtml-border-radius-topleft: 8px;	
	                -moz-border-radius-topleft: 8px;
	                border-top-left-radius: 8px;
					-webkit-border-top-right-radius: 8px;
	                -khtml-border-radius-topright: 8px;	
	                -moz-border-radius-topright: 8px;
	                border-top-right-radius: 8px;
					-webkit-border-bottom-left-radius: 8px;
	                -khtml-border-radius-bottomleft: 8px;	
	                -moz-border-radius-bottomleft: 8px;
	                border-bottom-left-radius: 8px;
					-webkit-border-bottom-right-radius: 8px;
	                -khtml-border-radius-bottomright: 8px;	
	                -moz-border-radius-bottomright: 8px;
	                border-bottom-right-radius: 8px;">
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td width="10"></td>
                                                                    <td height="6"></td>
                                                                    <td height="6"></td>
                                                                </tr>
                                                            </table>
SECONDARYARTICLENOTITLEBEGINPRINT;

$template['print']['whole']['main']['para'] = '<p style="font-family: Helvetica, Arial, sans-serif; font-size: 13px; margin-top: 0px; margin-bottom: 0px; padding: 0px;"><!--CONTENT--></p>';

$template['print']['whole']['main']['list'] = '<p style="font-family: Helvetica, Arial, sans-serif; list-style: none; font-size: 13px; margin-top: 0px; margin-bottom: 5px; padding: 0px;"><img src="<!--FULL TEMPLATE IMAGE PATH-->li.png" height="14" width="13" /><!--CONTENT--></p>';

$template['print']['whole']['main']['image'] = '<a href="<!--FULL IMAGE PATH--><!--CONTENT-->"><img style="-moz-box-shadow: 2px 2px 4px #000; -webkit-box-shadow: 2px 2px 4px #000; box-shadow: 2px 2px 4px #000; margin: 0; padding: 0; display: block;" src="<!--FULL IMAGE PATH--><!--CONTENT-->" width="309" /></a>';

$template['print']['whole']['secondary']['para'] = $template['print']['whole']['main']['para'];

$template['print']['whole']['secondary']['list'] = $template['print']['whole']['main']['list'];

$template['print']['whole']['secondary']['image'] = '<a href="<!--FULL IMAGE PATH--><!--CONTENT-->"><img style="-moz-box-shadow: 2px 2px 4px #000; -webkit-box-shadow: 2px 2px 4px #000; box-shadow: 2px 2px 4px #000; margin: 0; padding: 0; display: block;" src="<!--FULL IMAGE PATH--><!--CONTENT-->" width="204" /></a>';


$template['print']['whole']['header']['link']= '<!--LINK TEXT--> (<!--LINK URL-->)';
$template['print']['whole']['secondary']['link'] = $template['print']['whole']['main']['link'] = $template['print']['whole']['footer']['link'] = $template['print']['whole']['header']['link'];

/*


$templateData['web']['file'] = <<<WEBFILE
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><!--TITLE_ONSET--> <!--TITLE_CODA--> - Issue <!--NUM--></title>
<!--[if gte mso 9]>
<style type="text/css">
.body { background: #234865 url('<!--TEMPLATE IMAGE PATH-->body-bg.jpg'); }	     
.case { background: none; }
</style>
<![endif]-->
</head>
<body class="body" style="margin: 0; padding: 0; background-color: #234865; background-image: url(<!--TEMPLATE IMAGE PATH-->body-bg.jpg); background-repeat: repeat;" marginheight="0" topmargin="0" marginwidth="0" leftmargin="0">
<!--100% body table-->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="case" bgcolor="#234865" style="background-color: #234865; background-image: url(<!--TEMPLATE IMAGE PATH-->body-bg.jpg); background-repeat: repeat;">
            <!--[if gte mso 9]>
<td style="background: none;">
<![endif]-->
            <!--header text-->
            <table style="background-image: url(<!--TEMPLATE IMAGE PATH-->header-lines.jpg); background-position: bottom; background-repeat: repeat-x;" bgcolor="#1d3952" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <table id="top" width="578" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td valign="top" align="center" height="60">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td height="10"></td>
                                        </tr>
                                    </table>
                                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #4484b5; margin: 0; padding: 0;"><!--SUBSCRIBE--></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!--/header text-->
            <!--intro-->
            <table width="578" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td valign="top" height="20"></td>
                </tr>
                <tr>
                    <td background="<!--TEMPLATE IMAGE PATH-->header-bg.jpg" bgcolor="#c6d2db" valign="top" height="79">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td height="22" width="68%"></td>
                                <td height="20" width="32%"></td>
                            </tr>
                            <tr>
                                <td valign="top">
                                    <table width="82%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="center" valign="bottom" width="7%">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td width="15"></td>
                                                        <td width="10"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td valign="middle" width="93%">
                                                <h1 style="font-family: Helvetica, Arial, sans-serif; font-size: 24px; margin: 0px; padding: 0px; text-shadow: 1px 1px 1px #FFFFFF; color: #244a67"><!--TITLE_ONSET--><span style=" font-weight: normal;"><!--TITLE_CODA--></span></h1>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td valign="top">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td height="10"></td>
                                        </tr>
                                    </table>
                                    <p style=" font-family:Helvetica, Arial, sans-serif; font-weight: bold; font-size: 12px; margin: 0px; padding: 0px; text-shadow: 1px 1px 1px #FFFFFF; color: #336791">
                                        Issue <!--NUM--> - <!--DATE-->
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" height="20"></td>
                </tr>
            </table>
            <!--/intro-->
            <!--content section-->
            <table width="576" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="350" valign="top">
                        <table width="100%" border="0" cellpadding="20" cellspacing="0">
                            <tr>
                                <td valign="top" bgcolor="#1c3851" background="<!--TEMPLATE IMAGE PATH-->content-bg.jpg" style="border: solid 1px #193044; border-radius: 8px; -moz-border-radius: 8px; -webkit-border-radius: 8px; -khtml-border-radius: 8px;">
				     <!--MAIN-->
                                </td>
                            </tr>
                        </table>
                        <!--break-->
                    </td>
                    <td valign="top">
                    
                    <!--SECONDARY-->
                        
                    </td>
                </tr>
            </table>
            <!--break-->
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td height="25"></td>
                </tr>
            </table>
            <!--/break-->
            <!--footer-->
            <table style="background-image: url(<!--TEMPLATE IMAGE PATH-->footer-lines.jpg); background-position: top; background-repeat: repeat-x;" bgcolor="#1c293b" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <table width="578" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td valign="top" align="center" height="60">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td height="15"></td>
                                        </tr>
                                    </table>
                                    <p style="font-family: Helvetica, Arial, sans-serif; font-size: 13px; margin: 0px; padding: 0px; color: #30648d; text-shadow: 1px 1px 1px #000;"><!--PERSONAL WEBSITE--></p>
                                    <p style="font-family: Helvetica, Arial, sans-serif; font-size: 13px; margin: 0px; padding: 0px; color: #30648d; text-shadow: 1px 1px 1px #000;"><!--ADDRESS--></p>
                                    <p style="font-family: Helvetica, Arial, sans-serif; font-size: 13px; margin: 0px; padding: 0px; color: #30648d; text-shadow: 1px 1px 1px #000;"><!--ORG WEBSITE--></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!--/footer-->
        </td>
    </tr>
</table>
<!--/100% body table-->
</body>
</html>
WEBFILE;


$templateData['web']['mainArticle'] = '<!--CONTENT-->';

$templateData['web']['betweenMainArticles'] = $break . $hr . $break;

$templateData['web']['mainItem']['title'] = '<h2 style="font-family: Helvetica, Arial, sans-serif; font-size: 18px; margin: 0px; padding: 0px; text-shadow: 1px 1px 1px #333333; color: #62b6ee;"><!--CONTENT--></h2>';

$templateData['web']['mainItem']['para'] = '<p style="font-family: Helvetica, Arial, sans-serif; font-size: 13px; margin-top: 0px; margin-bottom: 0px; padding: 0px; color: #e7eff6;"><!--CONTENT--></p>';

$templateData['web']['mainItem']['list'] = '<p style="font-family: Helvetica, Arial, sans-serif; list-style: none; font-size: 13px; margin-top: 0px; margin-bottom: 5px; padding: 0px; color: #e7eff6"><img src="<!--TEMPLATE IMAGE PATH-->li.png" height="14" width="13" /><!--CONTENT--></p>';

$templateData['web']['mainItem']['image'] = '<a href="<!--IMAGE PATH--><!--CONTENT-->"><img style="-moz-box-shadow: 2px 2px 4px #000; -webkit-box-shadow: 2px 2px 4px #000; box-shadow: 2px 2px 4px #000; margin: 0; padding: 0; display: block;" src="<!--IMAGE PATH--><!--CONTENT-->" width="309" /></a>';

$templateData['web']['mainItem']['betweenItems'] = $break;

$templateData['web']['secondaryArticle'] = <<<SECONDARY

                        <!--section-->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="21">&nbsp;</td>
                                            <td>
                                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td height="17" width="204" valign="top" bgcolor="#1c3851" background="<!--TEMPLATE IMAGE PATH-->content-bg.jpg" style="border: solid 1px #193044; 
					-webkit-border-top-left-radius: 8px;
	                -khtml-border-radius-topleft: 8px;	
	                -moz-border-radius-topleft: 8px;
	                border-top-left-radius: 8px;
					-webkit-border-top-right-radius: 8px;
	                -khtml-border-radius-topright: 8px;	
	                -moz-border-radius-topright: 8px;
	                border-top-right-radius: 8px;
				    border-bottom: none;"> </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td width="19">&nbsp;</td>
                                        </tr>
                                    </table>
                                    <!--CONTENT-->
                                    
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td width="19">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <!--/section-->
                        
SECONDARY;


$templateData['web']['betweenSecondaryArticles'] = $bigBreak;


// BUG: this will break if a title comes anywhere other than first up in a secondary article
$templateData['web']['secondaryItem']['title'] =<<<SECONDARYTITLE

<table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td height="38" align="center">
                                                <table width="202" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td valign="top" style="background-image: url(<!--TEMPLATE IMAGE PATH-->ribbon.jpg); background-repeat: no-repeat; background-position: center;" bgcolor="#2b7ab7" height="38">
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td width="10"></td>
                                                                    <td height="6"></td>
                                                                    <td height="6"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                    <td>
                                                                        <p style="font-family: Helvetica, Arial, sans-serif; font-size: 13px; margin-top: 0px; margin-bottom: 0px; padding: 0px; color: #1f2d41; font-size: 16px; font-weight: bold; text-shadow: 0px 1px 1px #ffffff"><!--CONTENT--></p>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="20">&nbsp;</td>
                                            <td>
                                                <table width="100%" border="0" cellpadding="20" cellspacing="0">
                                                    <tr>
                                                        <td width="204" valign="top" bgcolor="#1c3851" background="<!--TEMPLATE IMAGE PATH-->content-bg.jpg" style="border: solid 1px #193044; 
					-webkit-border-bottom-left-radius: 8px;
	                -khtml-border-radius-bottomleft: 8px;	
	                -moz-border-radius-bottomleft: 8px;
	                border-bottom-left-radius: 8px;
					-webkit-border-bottom-right-radius: 8px;
	                -khtml-border-radius-bottomright: 8px;	
	                -moz-border-radius-bottomright: 8px;
	                border-bottom-right-radius: 8px;
				    border-top: none;">
				    
SECONDARYTITLE;

$templateData['web']['secondaryItem']['para'] = $templateData['web']['mainItem']['para'];

$templateData['web']['secondaryItem']['list'] = $templateData['web']['mainItem']['list'];

$templateData['web']['secondaryItem']['image'] = '<a href="<!--IMAGE PATH--><!--CONTENT-->"><img style="-moz-box-shadow: 2px 2px 4px #000; -webkit-box-shadow: 2px 2px 4px #000; box-shadow: 2px 2px 4px #000; margin: 0; padding: 0; display: block;" src="<!--IMAGE PATH--><!--CONTENT-->" width="204" /></a>';

$templateData['web']['secondaryItem']['betweenItems'] = "\n";

$templateData['web']['linkStyle'] = 'color:#62b6ee;';


$templateData['print'] = $templateData['email'] = $templateData['web'];

$templateData['email']['file'] = <<<EMAILFILE
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><!--TITLE_ONSET--> <!--TITLE_CODA--> - Issue <!--NUM--></title>
<!--[if gte mso 9]>
<style type="text/css">
.body { background: #234865 url('<!--FULL TEMPLATE IMAGE PATH-->body-bg.jpg'); }	     
.case { background: none; }
</style>
<![endif]-->
</head>
<body class="body" style="margin: 0; padding: 0; background-color: #234865; background-image: url(<!--FULL TEMPLATE IMAGE PATH-->body-bg.jpg); background-repeat: repeat;" marginheight="0" topmargin="0" marginwidth="0" leftmargin="0">
<!--100% body table-->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="case" bgcolor="#234865" style="background-color: #234865; background-image: url(<!--FULL TEMPLATE IMAGE PATH-->body-bg.jpg); background-repeat: repeat;">
            <!--[if gte mso 9]>
<td style="background: none;">
<![endif]-->
            <!--header text-->
            <table style="background-image: url(<!--FULL TEMPLATE IMAGE PATH-->header-lines.jpg); background-position: bottom; background-repeat: repeat-x;" bgcolor="#1d3952" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <table id="top" width="578" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td valign="top" height="60">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td height="10"></td>
                                        </tr>
                                    </table>
                                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #4484b5; margin: 0; padding: 0;">Dear <!--SALUTATION-->,</p>
                                   <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #4484b5; margin: 0; padding: 0;"><!--GREETING--></p>
                                 </td>
                             </tr>
                             <tr>
                                 <td valign="top" align="center" height="60">
                                     <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #4484b5; margin: 0; padding: 0;">You’re receiving this newsletter because you expressed interest in hearing from us.</p>
                                     <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #4484b5; margin: 0; padding: 0;">Having trouble reading this email? <a style="color:#62b6ee;" href="<!--WEB VERSION-->">View it in your browser.</a>
                                         |<!--UNSUBSCRIBE--></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!--/header text-->
            <!--intro-->
            <table width="578" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td valign="top" height="20"></td>
                </tr>
                <tr>
                    <td background="<!--FULL TEMPLATE IMAGE PATH-->header-bg.jpg" bgcolor="#c6d2db" valign="top" height="79">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td height="22" width="68%"></td>
                                <td height="20" width="32%"></td>
                            </tr>
                            <tr>
                                <td valign="top">
                                    <table width="82%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="center" valign="bottom" width="7%">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td width="15"></td>
                                                        <td width="10"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td valign="middle" width="93%">
                                                <h1 style="font-family: Helvetica, Arial, sans-serif; font-size: 24px; margin: 0px; padding: 0px; text-shadow: 1px 1px 1px #FFFFFF; color: #244a67"><!--TITLE_ONSET--><span style=" font-weight: normal;"><!--TITLE_CODA--></span></h1>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td valign="top">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td height="10"></td>
                                        </tr>
                                    </table>
                                    <p style=" font-family:Helvetica, Arial, sans-serif; font-weight: bold; font-size: 12px; margin: 0px; padding: 0px; text-shadow: 1px 1px 1px #FFFFFF; color: #336791">
                                        Issue <!--NUM--> - <!--DATE-->
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" height="20"></td>
                </tr>
            </table>
            <!--/intro-->
            <!--content section-->
            <table width="576" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="350" valign="top">
                        <table width="100%" border="0" cellpadding="20" cellspacing="0">
                            <tr>
                                <td valign="top" bgcolor="#1c3851" background="<!--FULL TEMPLATE IMAGE PATH-->content-bg.jpg" style="border: solid 1px #193044; border-radius: 8px; -moz-border-radius: 8px; -webkit-border-radius: 8px; -khtml-border-radius: 8px;">
				     <!--MAIN-->
                                </td>
                            </tr>
                        </table>
                        <!--break-->
                    </td>
                    <td valign="top">
                    
                    <!--SECONDARY-->
                        
                    </td>
                </tr>
            </table>
            <!--break-->
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td height="25"></td>
                </tr>
            </table>
            <!--/break-->
            <!--footer-->
            <table style="background-image: url(<!--FULL TEMPLATE IMAGE PATH-->footer-lines.jpg); background-position: top; background-repeat: repeat-x;" bgcolor="#1c293b" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <table width="578" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td valign="top" align="center" height="60">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td height="15"></td>
                                        </tr>
                                    </table>
                                    <p style="font-family: Helvetica, Arial, sans-serif; font-size: 13px; margin: 0px; padding: 0px; color: #30648d; text-shadow: 1px 1px 1px #000;"><!--PERSONAL WEBSITE--></p>
                                    <p style="font-family: Helvetica, Arial, sans-serif; font-size: 13px; margin: 0px; padding: 0px; color: #30648d; text-shadow: 1px 1px 1px #000;"><!--ADDRESS--></p>
                                    <p style="font-family: Helvetica, Arial, sans-serif; font-size: 13px; margin: 0px; padding: 0px; color: #30648d; text-shadow: 1px 1px 1px #000;"><!--PHONE--> <!--SKYPE--></p>
                                    <p style="font-family: Helvetica, Arial, sans-serif; font-size: 13px; margin: 0px; padding: 0px; color: #30648d; text-shadow: 1px 1px 1px #000;"><!--ORG WEBSITE--></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!--/footer-->
        </td>
    </tr>
</table>
<!--/100% body table-->
</body>
</html>
EMAILFILE;

$templateData['email']['betweenMainArticles'] = str_replace('<!--TEMPLATE IMAGE PATH-->', '<!--FULL TEMPLATE IMAGE PATH-->', $templateData['web']['betweenMainArticles']);

$templateData['email']['mainItem']['list'] = str_replace('<!--TEMPLATE IMAGE PATH-->', '<!--FULL TEMPLATE IMAGE PATH-->', $templateData['web']['mainItem']['list']);

$templateData['email']['mainItem']['image'] = str_replace('<!--IMAGE PATH-->', '<!--FULL IMAGE PATH-->', $templateData['web']['mainItem']['image']);

$templateData['email']['secondaryItem']['title'] = str_replace('<!--TEMPLATE IMAGE PATH-->', '<!--FULL TEMPLATE IMAGE PATH-->', $templateData['web']['secondaryItem']['title']);

$templateData['email']['secondaryItem']['list'] = $templateData['email']['mainItem']['list'];

$templateData['email']['secondaryItem']['image'] = str_replace('<!--IMAGE PATH-->', '<!--FULL IMAGE PATH-->', $templateData['web']['secondaryItem']['image']);

$templateData['print']['file'] = <<<PRINTFILE
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><!--TITLE_ONSET--> <!--TITLE_CODA--> - Issue <!--NUM--></title>
<!--[if gte mso 9]>
<style type="text/css">
.body { background: none; }	     
.case { background: none; }
</style>
<![endif]-->
</head>
<body class="body" style="margin: 0; padding: 0; background-color: #234865;" marginheight="0" topmargin="0" marginwidth="0" leftmargin="0">
<!--100% body table-->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="case">
            <!--[if gte mso 9]>
<td style="background: none;">
<![endif]-->
            
            <!--intro-->
            <table width="578" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td valign="top" height="20"></td>
                </tr>
                <tr>
                    <td valign="top" height="79">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td height="22" width="68%"></td>
                                <td height="20" width="32%"></td>
                            </tr>
                            <tr>
                                <td valign="top">
                                    <table width="82%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="center" valign="bottom" width="7%">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td width="15"></td>
                                                        <td width="10"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td valign="middle" width="93%">
                                                <h1 style="font-family: Helvetica, Arial, sans-serif; font-size: 24px; margin: 0px; padding: 0px; color: #000000"><!--TITLE_ONSET--><span style=" font-weight: normal;"><!--TITLE_CODA--></span></h1>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td valign="top">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td height="10"></td>
                                        </tr>
                                    </table>
                                    <p style=" font-family:Helvetica, Arial, sans-serif; font-weight: bold; font-size: 12px; margin: 0px; padding: 0px; color: #111111">
                                         Issue <!--NUM--> - <!--DATE-->
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" height="20"></td>
                </tr>
            </table>
            <!--/intro-->
            <!--content section-->
            <table width="576" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="350" valign="top">
                        <table width="100%" border="0" cellpadding="20" cellspacing="0">
                            <tr>
                                <td valign="top" style="border: solid 1px #444444; border-radius: 8px; -moz-border-radius: 8px; -webkit-border-radius: 8px; -khtml-border-radius: 8px;">
				     <!--MAIN-->
                                </td>
                            </tr>
                        </table>
                        <!--break-->
                    </td>
                    <td valign="top">
                    
                    <!--SECONDARY-->
                        
                    </td>
                </tr>
            </table>
            <!--break-->
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td height="25"></td>
                </tr>
            </table>
            <!--/break-->
            <!--footer-->
            <table style="background-image: url(<!--TEMPLATE IMAGE PATH-->footer-lines.jpg); background-position: top; background-repeat: repeat-x;" bgcolor="#1c293b" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <table width="578" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td valign="top" align="center" height="60">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td height="15"></td>
                                        </tr>
                                    </table>
                                    <p style="font-family: Helvetica, Arial, sans-serif; font-size: 13px; margin: 0px; padding: 0px; color: #30648d; text-shadow: 1px 1px 1px #000;"><!--PERSONAL WEBSITE--></p>
                                    <p style="font-family: Helvetica, Arial, sans-serif; font-size: 13px; margin: 0px; padding: 0px; color: #30648d; text-shadow: 1px 1px 1px #000;"><!--ADDRESS--></p>
                                    <p style="font-family: Helvetica, Arial, sans-serif; font-size: 13px; margin: 0px; padding: 0px; color: #30648d; text-shadow: 1px 1px 1px #000;"><!--ORG WEBSITE--></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!--/footer-->
        </td>
    </tr>
</table>
<!--/100% body table-->
</body>
</html>
PRINTFILE

*/

?>
