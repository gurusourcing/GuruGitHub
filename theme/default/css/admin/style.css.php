
@charset "utf-8";
/* CSS Document */
/* General Styles */
* {margin:0; padding:0;}
html, body, p, div, h1, h2, h3, h4, h5, h6, img, span, ul, li, br, hr, a, form, input,  dl, dt, dd, iframe { padding:0px; margin:0px; border:0px;}
img {margin:0; padding:0; outline-style:none; outline-width:0; }
input {margin:0; padding:0; outline-style:none; outline-width:0; }

a, a:hover, a:active, a:focus { outline:none; outline-style:none; outline-width:0;}

body {margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; color:#000000;}

.clr { font-size:0; height:0; line-height:0; clear:both;}
.left {margin:0; padding:0; width:auto; height:auto; float:left;}
.right {margin:0; padding:0; width:auto; height:auto; float:right;}

	#header {margin:0 auto; padding:0; width:100%; height:auto;}
	#header #logo {margin:0; padding:15px; width:auto; height:auto; float:left;}
	#header #toplink {margin:0; padding:15px; width:auto; height:auto; float:right;}
	#header #toplink ul {margin:0; padding:0; list-style:none;}
	#header #toplink ul li {margin:0; padding:0 5px; font:normal 12px/20px Arial, Helvetica, sans-serif; color:#000000; float:left; display:block;}
	#header #toplink ul li a {color:#000000; text-decoration:none; display:block;}
	#header #toplink ul li a:hover {color:#e62d2b;}
	
#navigation {margin:0 auto; padding:0; width:100%; height:34px; background:url(../../images/admin/tab_r.png) repeat-x left top;}

#content {margin:0 auto; padding:25px 0; width:100%; height:auto;}
	
	#welcome_box {margin:0 auto; padding:30px 5px; width:500px; height:auto; font:normal 24px/24px Arial, Helvetica, sans-serif; color:#e92f20;}
	
	#black_box {margin:0 auto; margin-bottom:100px; padding:20px; width:470px; height:auto; background-color:#3a3c3d; border-radius:5px; -webkit-border-radius:5px; -moz-border-radius:5px; border:2px solid #e92f20;}
	#black_box p {font:normal 16px/20px Arial, Helvetica, sans-serif; color:#FFFFFF;}
	#black_box .lable {margin:10px 0; padding:0 20px 0 0; width:150px; height:auto; float:left; font:normal 16px/28px Arial, Helvetica, sans-serif; color:#FFFFFF; text-align:right;}
	#black_box .field {margin:10px 0; padding:0; width:300px; height:auto; float:left;}
	#black_box .field input[type="text"] {margin:0; padding:5px; width:auto; height:auto; background-color:#c1c2c2; border:1px solid #000000; font:normal 14px Arial, Helvetica, sans-serif; color:#000000;}
	#black_box .field input[type="password"] {margin:0; padding:5px; width:auto; height:auto; background-color:#c1c2c2; border:1px solid #000000; font:normal 14px Arial, Helvetica, sans-serif; color:#000000;}
	#black_box .field input[type="submit"] {margin:0; padding:0; width:100px; height:30px; background:url(../../images/admin/btn.png) no-repeat center top; border:none; font:bold 14px Arial, Helvetica, sans-serif; color:#FFFFFF; text-transform:uppercase; cursor:pointer;}
	
	#show_hide {margin:0; padding:0; float:left;}
	#left_panel {margin:0; padding:0; width:15%; height:560px; float:left; border:1px solid #a7a7a7; border-width:1px 0 0 0;}
	#left_panel h4 {margin:10px 10px 0 10px; padding:0; font:bold 14px Arial, Helvetica, sans-serif; color:#626262; border-bottom:2px solid #a7a7a7;}
	#left_panel ul.link {margin:0 10px; padding:0; list-style:none;}
	#left_panel ul.link li {margin:0; padding:0; font:normal 11px/16px Arial, Helvetica, sans-serif; color:#626262; border-bottom:1px solid #a7a7a7;}
	#left_panel ul.link li a {color:#626262; text-decoration:none; display:block; padding:3px 0;}
	#left_panel ul.link li a:hover {color:#000000; text-decoration:none; background-color:#f9f9f9;}
	#left_panel ul.link li a img {float:left; margin:0 5px;}
	#left_panel .form_box {margin:0; padding:10px; width:auto; float:left;}
	#left_panel .form_box .lable {margin:0; padding:0; width:auto; float:left; font:normal 11px/16px Arial, Helvetica, sans-serif; color:#626262; clear:both;}
	#left_panel .form_box .field {margin:0; padding:2px 0; width:auto; float:left; clear:both;}
	#left_panel .form_box .field input[type="text"] {margin:0; padding:2px; width:auto; height:auto; background-color:#FFFFFF; border:1px solid #a7a7a7; font:normal 11px Arial, Helvetica, sans-serif; color:#626262;}
	#left_panel .form_box input[type="button"] {margin:0; padding:0 8px; width:auto; height:20px; background:url(../../images/admin/btn.png) no-repeat center; border:none; font:bold 11px Arial, Helvetica, sans-serif; color:#FFFFFF; cursor:pointer;}
	
	#right_panel {margin:0; padding:1%; width:81.8%; float:right;  border:1px solid #a7a7a7;}
	#right_panel h2 {margin:0; padding:0; font:bold 16px/20px Arial, Helvetica, sans-serif; color:#626262;}
	
	#tabbar {margin:10px 0 0 0; padding:0; width:100%; height:30px; float:left;}
	#tabbar ul {margin:0; padding:0; list-style:none;}
	#tabbar ul li {margin:0 2px 0 0; padding:0; float:left; font:normal 14px/30px Arial, Helvetica, sans-serif; color:#FFFFFF;}
	#tabbar ul li a {margin:0; padding:0 10px; color:#FFFFFF; text-decoration: none; display:block; background:url(../../images/admin/blue_1a.gif) no-repeat center;}
	#tabbar ul li a:hover, #tabbar li a.select {background:url(../../images/admin/tab-left.png) no-repeat left; color:#FFFFFF; background:url(../../images/admin/blue_1.gif) no-repeat center;}
	
	#tabcontent {margin:0; padding:1%; width:98%; height:auto; float:left; border:1px solid #a7a7a7;}
	#tabcontent table td {margin:0; padding:4px; font:normal 11px Arial, Helvetica, sans-serif; color:#000000; background-color:#f9f9f9;}
	#right_panel input[type="text"] {margin:0; padding:2px; width:auto; height:auto; background-color:#FFFFFF; border:1px solid #a7a7a7; font:normal 11px Arial, Helvetica, sans-serif; color:#626262;}
	#right_panel select[multiple="multiple"] {margin:0; padding:2px; width:auto; height:auto; background-color:#FFFFFF; border:1px solid #a7a7a7; font:normal 11px Arial, Helvetica, sans-serif; color:#626262;}
	#right_panel select {margin:0; padding:1px 2px; width:auto; height:auto; background-color:#FFFFFF; border:1px solid #a7a7a7; font:normal 11px Arial, Helvetica, sans-serif; color:#626262;}
	#right_panel textarea {margin:0; padding:2px; width:auto; height:auto; background-color:#FFFFFF; border:1px solid #a7a7a7; font:normal 11px Arial, Helvetica, sans-serif; color:#626262;}
	#right_panel input[type="button"] {margin:0; padding:0 8px; width:auto; height:20px; background:url(../../images/admin/blue_1a.gif) no-repeat center; border:none; font:bold 11px Arial, Helvetica, sans-serif; color:#FFFFFF; cursor:pointer;}
	
	
	#accountlist {margin:10px 0 0 0; padding:1%; width:98%; height:auto; float:left; border:3px solid #a7a7a7; border-width:3px 0;}
	#accountlist .top {margin:10px 0 0 0; padding:0; width:100%; height:auto; float:left;}
	#accountlist .bot {margin:0; padding:0; width:100%; height:auto; float:left;}
	#accountlist .mid {margin:5px 0; padding:0; width:100%; height:auto; float:left;}
	#accountlist .mid table {border:1px solid #a7a7a7; border-collapse:collapse; width:99.8%;}
	#accountlist .mid table td {border:1px solid #a7a7a7; border-width:1px 0px; border-collapse:collapse; padding:0.5%; font:normal 12px Arial, Helvetica, sans-serif; color:#121314;}
	#accountlist .mid table th {border:1px solid #a7a7a7; border-width:1px 0px; border-collapse:collapse; padding:0.5%; font:bold 12px Arial, Helvetica, sans-serif; color:#121314; background-color:#f9f9f9;}
	#accountlist .mid table td a {color:#121314; text-decoration:underline;}
	#accountlist .mid table tr:hover { background-color:#f9f9f9;}
	
	
	.add_edit {margin:5px 0; padding:1%; width:98%; height:auto; float:left; border:3px solid #a7a7a7; border-width:3px 0 1px 0; background-color:#f9f9f9;}
	.add_edit table td {margin:0; padding:4px; font:normal 12px Arial, Helvetica, sans-serif; color:#000000;}
	.add_edit table th {margin:0; padding:4px; font:bold 12px Arial, Helvetica, sans-serif; color:#000000;}
	.add_edit table td a {color:#121314; text-decoration:underline;}
	
	.links {margin:0; padding:0; font:normal 12px/20px Arial, Helvetica, sans-serif; color:#000000;}
	 a.links {font:normal 12px/13px Arial, Helvetica, sans-serif; color:#000000; text-decoration:none;}
	 a.links:hover {color:#000000; text-decoration:underline;}
	 a.links img { float:left; margin:0 5px;}
	
	#tabbar2 {margin:0; padding:0; width:100%; height:22px; float:left;}
	#tabbar2 ul {margin:0; padding:0; list-style:none;}
	#tabbar2 ul li {margin:0 2px 0 0; padding:0; float:left; font:normal 12px/22px Arial, Helvetica, sans-serif; color:#FFFFFF;}
	#tabbar2 ul li a {margin:0; padding:0 10px; color:#FFFFFF; text-decoration: none; display:block; background:url(../../images/admin/blue_1a.gif) no-repeat center;}
	#tabbar2 ul li a:hover, #tabbar2 li a.select {background:url(../../images/admin/tab-left.png) no-repeat left; color:#FFFFFF; background:url(../../images/admin/blue_1.gif) no-repeat center;}
	
	
	.details_box {margin:0; padding:0 0 10px 0; width:100%; height:auto; float:left; border-bottom:2px solid #a7a7a7;}
	.details_box .heading {margin:0; padding:0; width:100%; height:auto; float:left; font:bold 12px/20px Arial, Helvetica, sans-serif; color:#000000;}
	.details_box .description {margin:0; padding:0; width:100%; height:auto; float:left;}
	.details_box .description table {border:1px solid #a7a7a7; border-collapse:collapse; width:99.8%;}
	.details_box .description table td {border:1px solid #a7a7a7; border-width:1px 0px; border-collapse:collapse; padding:0.5%; font:normal 12px Arial, Helvetica, sans-serif; color:#121314;}
	.details_box .description table th {border:1px solid #a7a7a7; border-width:1px 0px; border-collapse:collapse; padding:0.5%; font:bold 12px Arial, Helvetica, sans-serif; color:#121314; background-color:#f1f1f1;}
	.details_box .description table td a {color:#121314; text-decoration:underline;}
	.details_box .description table tr:hover {background-color:#f1f1f1;}
	.details_box .description .img {float:left; margin:2px;}
	
	.pagination li          { border:0; margin:0; padding:0; font-size:11px; list-style:none; /* savers */ float:left; }
	.pagination a           { border:solid 1px #a7a7a7; margin-right:2px; }
	.pagination .previous-off,
	.pagination .next-off   { border:solid 1px #DEDEDE; color:#888888; display:block; float:left; font-weight:bold; margin-right:2px; padding:2px 4px; }
	.pagination .next a,
	.pagination .previous a { font-weight:bold; }	
	.pagination .active     { background:url(../../images/admin/blue_1.gif) no-repeat center; color:#FFFFFF; font-weight:bold; display:block; float:left; padding:3px 6px; /* savers */ margin-right:2px; }
	.pagination a:link, 
	.pagination a:visited   { color:#0e509e; display:block; float:left; padding:2px 6px; text-decoration:none; }
	.pagination a:hover     { border:solid 1px #0e509e; }


#footer {margin:0 auto; padding:15px 0; width:100%; height:auto; border-top:1px solid #626262; text-align:center;}
	#footer p {font:normal 11px/16px Arial, Helvetica, sans-serif; color:#121314;}