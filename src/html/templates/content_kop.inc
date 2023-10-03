{doctype}<html>
  <head>
    <title>
       {title}
    </title>
    <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
    {style}
    {style2}
    {jsincludes}
    {htmleditorinclude}
    {calendarinclude}
    <link rel="stylesheet" href="style/fontAwesome/font-awesome.min.css">

    <script language="JavaScript" TYPE="text/javascript">
    {javascript}
    {openwindow}
    {htmleditorloader}
    {ajaxinclude}
    </script>
 </head>
<body text="#000000"  marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" {body} {htmleditorbody}>
{calendar}
{pageHeader}
<style>
.ui-autocomplete {
    max-height: 100px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
}
</style>