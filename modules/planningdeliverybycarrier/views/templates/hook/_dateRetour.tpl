<h2>{l s='Choisir une date de retour' mod='planningdeliverybycarrier'}</h2>

<fieldset>
<form method="post" action="{$action}" class="form">
    {l s='Date de retour' mod='planningdeliverybycarrier'} : <input type="text" name="datepickerDatelivraison" id="datepickerDatelivraison"/>
    {l s='Transporteur' mod='planningdeliverybycarrier'} : <select name="carrier_selected" id="carrier_selected" >{$carriers}</select>
    <input type="submit" class="button" name="submitDateLivraison" value="{l s='Envoyer' mod='planningdeliverybycarrier'}" />
     <a  class="button ExportAdresse" href="#">{l s='Exporter les adresses' mod='planningdeliverybycarrier'} </a>
    &nbsp;<a  class="button ExportCSV" href="#">{l s='Exporter les commandes' mod='planningdeliverybycarrier'} </a>
</form>
</fieldset>
 <script type="text/javascript">
    $(document).ready(function () {        
        function exportTableToText(me,$table, filename) {

            var $rows = $table.find('tr'),
                    // Temporary delimiter characters unlikely to be typed by keyboard
                    // This is to avoid accidentally splitting the actual contents
                   tmpColDelim = String.fromCharCode(11), // vertical tab character
                    tmpRowDelim = String.fromCharCode(0), // null character

                    // actual delimiter characters for CSV format
                    colDelim = '","',
                    rowDelim = '\r\n',
                    // Grab text from table into CSV formatted string
                    csv = $rows.map(function (i, row) {
                        var $row = $(row),
                                $cols = $row.find('td .addrExport');
                        return $cols.map(function (j, col) {
                            var $col = $(col),
                                    text = $col.data('adrexp');
                            return text.replace('"', '""'); // escape double quotes

                        }).get().join(tmpColDelim);

                    }).get().join(tmpRowDelim)
                    .split(tmpRowDelim).join(rowDelim)+rowDelim
//                         .split(tmpColDelim).join(colDelim) ,
                    ,
                    premierAdresse = "Impasse du Château, 1116 Cottens, Switzerland"+rowDelim,
                    // Data URI
                    csvData = 'data:text/plain;charset=utf-8,' + encodeURIComponent(premierAdresse+csv);

                $(me)
                    .attr({
                        'download': filename,
                        'href': csvData,
                        'target': '_blank'
                    });
        }
        
        

        function exportTableToCSV(me,$table, filename) {

            var $rows = $table.find('tr:has(td),tr:has(th)').not('.is_not_commande'),

                // Temporary delimiter characters unlikely to be typed by keyboard
                // This is to avoid accidentally splitting the actual contents
                tmpColDelim = String.fromCharCode(11), // vertical tab character
                tmpRowDelim = String.fromCharCode(0), // null character

                // actual delimiter characters for CSV format
                colDelim = '","',
                rowDelim = '";\r\n"',
                Aremplacer = new RegExp("(\r\n|\r|\n)", "g" ),
                // Grab text from table into CSV formatted string
                csv = '"' + $rows.map(function (i, row) {
                    var $row = $(row),
                        $cols = $row.find('td:eq(0),td:eq(1),td:eq(2),td:eq(3),td:eq(4),td:eq(5),td:eq(6),td:eq(7),th:eq(0),th:eq(1),th:eq(2),th:eq(3),th:eq(4),th:eq(5),th:eq(6),th:eq(7)');
                    return $cols.map(function (j, col) {
                        var $col = $(col),
                            text = $.trim($col.text());
                            if(j == 6){
                                if(col.tagName == "TD"){
                                    text = "";
                                    if($col.find('.message-body').length)
                                        $.each($col.find('.message-body').data('messages'),function(k,v){
                                            text += v.toString().replace(Aremplacer, '')+" - ";
                                        });
                                }
                            }
                        return text.replace(/"/g, '""'); // escape double quotes

                    }).get().join(tmpColDelim);

                }).get().join(tmpRowDelim)
                    .split(tmpRowDelim).join(rowDelim)
                    .split(tmpColDelim).join(colDelim) + '"',

                // Data URI
                csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);                
            $(me)
                .attr({
                'download': filename,
                    'href': csvData,
                    'target': '_blank'
            });
        }
        
        
        // This must be a hyperlink
        $("a.ExportAdresse").on('click', function (event) {
            //event.preventDefault();
            var me = $(this),
            table = $('table.table.order tbody');
            if(table.find('tr td .addrExport').length <=0){
                alert("Il faut choisir d'abord une date de livraison et un transporteur qui contient des livraisons");
                return  false;
            }else{
               var d = new Date();
            // text
               exportTableToText(this,table, 'export_adresse_'+d.getTime()+'.txt');
               return;
            }

            // IF CSV, don't do event.preventDefault() or return false
            // We actually need this to be a typical hyperlink
        });
        
        $("a.ExportCSV").on('click', function (event) {
            //event.preventDefault();
            var me = $(this),
            table = $('table.table.order ');
            if(table.find('tr td ').length <=0){
                alert("Il faut choisir d'abord une date de livraison et un transporteur qui contient des livraisons");
                return  false;
            }else{
               var d = new Date();
            // csv
               exportTableToCSV(this,table, 'export_csv_'+d.getTime()+'.csv');
               return;
            }

            // IF CSV, don't do event.preventDefault() or return false
            // We actually need this to be a typical hyperlink
        });
        
    });
</script>