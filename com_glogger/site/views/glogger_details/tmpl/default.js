jQuery(document).ready(function () {
    jQuery(".open-window").click(function(){
        var source = "#"+jQuery(this).attr("id")+"-html";
        var w = window.open("", jQuery(this).attr("id") ,"width=1024, height=768");
        var html = "<h2>" + jQuery(this).attr("title") + ":<Br/> \'" + jQuery("#item-title").text() + "\', \'" + jQuery("#item-source").text() + "\', \'" + jQuery("#item-username").text() + "\', \'" + jQuery("#item-remote_addr").text()+"\'</h2>"
        +jQuery(source).html();
        jQuery(w.document.body).html(html);

    });

    jQuery('#using_json_2').jstree(
        { 'core' : {
            'data' : LogsTree
            }
    });
});
