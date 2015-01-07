// Load the Visualization API and the piechart package.
google.load( 'visualization', '1.0', {'packages':['corechart']} );

function drawChart( type, target, values, title, pie3dDonnut ){

    // Create the data table.
    if( typeof pie3dDonnut == undefined ){
        donnut = 1;//pie 3d
    }


    var data = new google.visualization.arrayToDataTable( values);


    // Set chart options
    var options = {
        'title' :title,
        'tooltip': {
            'showColorCode': true
        }
    };

    switch ( pie3dDonnut ) {
        case 1: //pie 3d
            options.is3D = true;
            break;
        case 2: //pie 2D
            options.is3D = false;
            break;
        case 3: //donnut
            options.is3D = false;
            options.pieHole = 0.5
            break;
        default:
            options.id3D = false;
            options.pieHole = 0
            break;
    }
    var chart;
    // Instantiate and draw our chart, passing in some options.
    if ( type.toLowerCase() == "bar" ) {
        chart = new google.visualization.BarChart( document.getElementById( target ) );
    }
    if ( type.toLowerCase() == "pie" ) {
        chart = new google.visualization.PieChart( document.getElementById( target ) );
    }
    if ( type.toLowerCase() == "line" ) {
        chart = new google.visualization.LineChart( document.getElementById( target ) );
    }
    chart.draw( data, options );
}

jQuery( document ).ready( function (){
    jQuery( '#sCharts' ).change( function (){
        var opts = [];
        switch( jQuery(this ).val() ){
            case 'bar':
                chartType = 'bar';
                pie3dDonnut = 4;
                break;
            case 'line':
                chartType = 'line';
                pie3dDonnut = 4;
                break;
            case 'pie':
                chartType = 'pie';
                pie3dDonnut = 4;
                break;
            case 'pie3d':
                chartType = 'pie';
                pie3dDonnut = 1;
                break;
            case 'donnut':
                chartType = 'pie';
                pie3dDonnut = 3;
                break;
        }
        drawChart(
            chartType,
            'chart',
            [
                ["Answers", "Votes"],
                ["A1", 243],
                ["A2", 41],
                ["A3", 512],
                ["A4", 152],
                ["A5", 75]
            ],
            "Chart",
            pie3dDonnut
        );
    } )
} )