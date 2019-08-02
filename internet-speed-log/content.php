this is a log of my internet speeds as "speedtest-cli" reports them every half hour
<br>
<div class="chart" id="chart-bandwidth-down-24hrs"></div>
<div class="chart" id="chart-bandwidth-down-month"></div>
<div class="chart" id="chart-bandwidth-up-24hrs"></div>
<div class="chart" id="chart-bandwidth-up-month"></div>
<div class="chart" id="chart-latency-24hrs"></div>
<div class="chart" id="chart-latency-month"></div>

Raw Data

<table class="internet-speed-table">
    <thead>
        <tr>
            <th>Time</th>
            <th>Duration</th>
            <th>Source IP</th>
            <th>Test Host</th>
            <th>Test Host Distance</th>
            <th>Download</th>
            <th>Upload</th>
            <th>Latency</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<br>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var charts = [];

    var formatDate = function(unixTimestamp, showYear) {
        var dt = new Date(unixTimestamp * 1000);

        var month = dt.getMonth();
        var date = dt.getDate();
        var year = dt.getFullYear();
        var date_suffix = "th";
        if (date == 1) date_suffix = "st";
        if (date == 2) date_suffix = "nd";
        if (date == 3) date_suffix = "rd";

        var yeartxt = "";
        if (showYear === true) yeartxt = ", " + year;

        return months[month] + " " + date + date_suffix + yeartxt;
    };

    var formatTime = function(unixTimestamp, showSeconds) {
        var dt = new Date(unixTimestamp * 1000);

        var hours = dt.getHours();
        var minutes = dt.getMinutes();
        var seconds = dt.getSeconds();

        var ampm = "AM";
        if (hours > 12) {
            ampm = "PM";
            hours -= 12;
        }

        if (hours == 0) hours = 12;

        // the above dt.get...() functions return a single digit
        // so I prepend the zero here when needed
        if (hours < 10) 
        hours = "0" + hours;

        if (minutes < 10) 
        minutes = "0" + minutes;

        if (seconds < 10) 
        seconds = "0" + seconds;

        var secondstxt = "";
        if (showSeconds === true) secondstxt = ":" + seconds;

        return hours + ":" + minutes + secondstxt + " " + ampm;
    };

    var formatDuration = function(seconds) {
        var sec_num = parseInt(seconds, 10);
        var hours = Math.floor(sec_num / 3600);
        var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
        var seconds = sec_num - (hours * 3600) - (minutes * 60);

        if (hours < 10) { hours = "0" + hours; }
        if (minutes < 10) { minutes = "0" + minutes; }
        if (seconds < 10) { seconds = "0" + seconds; }

        var hourstxt = "";
        if (hours > 0) hourstxt = hours + ":";

        var time = hourstxt + minutes + ":" + seconds;
        return time;
    };

    $(function() {
        // Load the Visualization API and the corechart package.
        google.charts.load("current", {"packages": ["corechart"]});

        $(window).resize(function() {
            for (var i = 0; i < charts.length; i++) {
                charts[i].chart.draw(charts[i].data, charts[i].options);
            }
        });

        $.post("internet-speed-data.php", {"action": "get_data", "timeframe": 60 * 60 * 24}, function(res) {
            if (res.status == 0 || res.status == 2) {
                if (res.status == 2) {
                    alert("Warning loading 24 hour data: " + res.message);
                }

                var data_24hr = res.data;

                var table_html = "";
                for (var i = 0; i < data_24hr.length; i++) {
                    table_html = "<tr><td>" + formatDate(data_24hr[i].timestamp, true) + " " + formatTime(data_24hr[i].timestamp) + "</td><td>" + formatDuration(data_24hr[i].duration) + "</td><td>" + data_24hr[i].source_ip_addr + "</td><td>" + data_24hr[i].test_host + "</td><td>" + data_24hr[i].test_host_distance + " km</td><td>" + data_24hr[i].download + " Mbit/s</td><td>" + data_24hr[i].upload + " Mbit/s</td><td>" + data_24hr[i].latency + " ms</td></tr>" + table_html;
                }
                $(".internet-speed-table tbody").html(table_html);

                google.charts.setOnLoadCallback(function() {
                    var chart_data = new google.visualization.DataTable();
                    chart_data.addColumn("string", "Time");
                    chart_data.addColumn("number", "Download Bandwidth (Mbit/s)");

                    var inter_chart_data = [];
                    for (var i = 0; i < data_24hr.length; i++) {
                        inter_chart_data.push([formatTime(data_24hr[i].timestamp), data_24hr[i].download]);
                    }

                    chart_data.addRows(inter_chart_data);

                    // Set chart options
                    var chart_options = {
                        title: "Internet Download Bandwidth Over Past 24 Hours",
                        backgroundColor: {fill: "transparent"},
                        chartArea: {width: "75%"},
                        legend: "none",
                        hAxis: {
                            title: "Time",
                            titleTextStyle: {
                                bold: true,
                                italic: false
                            }
                        },
                        vAxis: {
                            title: "Bandwidth (Mbit/s)",
                            titleTextStyle: {
                                bold: true,
                                italic: false
                            },
                            minValue: 0
                        }
                    };

                    // Instantiate and draw our chart, passing in some options.
                    var chart = new google.visualization.AreaChart(document.getElementById("chart-bandwidth-down-24hrs"));
                    chart.draw(chart_data, chart_options);
                    charts.push({chart: chart, data: chart_data, options: chart_options});




                    chart_data = new google.visualization.DataTable();
                    chart_data.addColumn("string", "Time");
                    chart_data.addColumn("number", "Upload Bandwidth (Mbit/s)");

                    inter_chart_data = [];
                    for (var i = 0; i < data_24hr.length; i++) {
                        inter_chart_data.push([formatTime(data_24hr[i].timestamp), data_24hr[i].upload]);
                    }

                    chart_data.addRows(inter_chart_data);

                    // Set chart options
                    chart_options = {
                        title: "Internet Upload Bandwidth Over Past 24 Hours",
                        backgroundColor: {fill: "transparent"},
                        chartArea: {width: "75%"},
                        legend: "none",
                        series: {0: {color: "#e2431e"}},
                        hAxis: {
                            title: "Time",
                            titleTextStyle: {
                                bold: true,
                                italic: false
                            }
                        },
                        vAxis: {
                            title: "Bandwidth (Mbit/s)",
                            titleTextStyle: {
                                bold: true,
                                italic: false
                            },
                            minValue: 0
                        }
                    };

                    // Instantiate and draw our chart, passing in some options.
                    chart = new google.visualization.AreaChart(document.getElementById("chart-bandwidth-up-24hrs"));
                    chart.draw(chart_data, chart_options);
                    charts.push({chart: chart, data: chart_data, options: chart_options});




                    chart_data = new google.visualization.DataTable();
                    chart_data.addColumn("string", "Time");
                    chart_data.addColumn("number", "Latency (ms)");

                    inter_chart_data = [];
                    for (var i = 0; i < data_24hr.length; i++) {
                        inter_chart_data.push([formatTime(data_24hr[i].timestamp), data_24hr[i].latency]);
                    }

                    chart_data.addRows(inter_chart_data);

                    // Set chart options
                    chart_options = {
                        title: "Internet Latency Over Past 24 Hours",
                        backgroundColor: {fill: "transparent"},
                        chartArea: {width: "75%"},
                        legend: "none",
                        series: {0: {color: "#6f9654"}},
                        hAxis: {
                            title: "Time",
                            titleTextStyle: {
                                bold: true,
                                italic: false
                            }
                        },
                        vAxis: {
                            title: "Latency (ms)",
                            titleTextStyle: {
                                bold: true,
                                italic: false
                            },
                            minValue: 0
                        }
                    };

                    // Instantiate and draw our chart, passing in some options.
                    chart = new google.visualization.AreaChart(document.getElementById("chart-latency-24hrs"));
                    chart.draw(chart_data, chart_options);
                    charts.push({chart: chart, data: chart_data, options: chart_options});
                });
            }else{
                alert("Error loading 24 hour data: " + res.message);
            }
        }, "json");

        $.post("internet-speed-data.php", {"action": "get_data", "timeframe": 60 * 60 * 24 * 30}, function(res) {
            if (res.status == 0 || res.status == 2) {
                if (res.status == 2) {
                    alert("Warning loading month data: " + res.message);
                }

                var data_month = {};

                for (var i = 0; i < res.data.length; i++) {
                    var date = String(formatDate(res.data[i].timestamp));
                    if (!(date in data_month)) {
                        data_month[date] = {download: 0, upload: 0, latency: 0, dataCount: 0};
                    }
                    data_month[date].download += res.data[i].download;
                    data_month[date].upload += res.data[i].upload;
                    data_month[date].latency += res.data[i].latency;
                    data_month[date].dataCount++;
                }

                google.charts.setOnLoadCallback(function() {
                    var chart_data = new google.visualization.DataTable();
                    chart_data.addColumn("string", "Date");
                    chart_data.addColumn("number", "Average Download Bandwidth (Mbit/s)");

                    var inter_chart_data = [];
                    for (var date in data_month) {
                        if (!data_month.hasOwnProperty(date)) continue;

                        inter_chart_data.push([date, Math.round(data_month[date].download / data_month[date].dataCount * 100) / 100]);
                    }

                    chart_data.addRows(inter_chart_data);

                    // Set chart options
                    chart_options = {
                        title: "Average Internet Download Bandwidth Over Past Month",
                        backgroundColor: {fill: "transparent"},
                        chartArea: {width: "75%"},
                        legend: "none",
                        series: {0: {color: "#43459d"}},
                        hAxis: {
                            title: "Date",
                            titleTextStyle: {
                                bold: true,
                                italic: false
                            }
                        },
                        vAxis: {
                            title: "Bandwidth (Mbit/s)",
                            titleTextStyle: {
                                bold: true,
                                italic: false
                            },
                            minValue: 0
                        }
                    };

                    // Instantiate and draw our chart, passing in some options.
                    var chart = new google.visualization.AreaChart(document.getElementById("chart-bandwidth-down-month"));
                    chart.draw(chart_data, chart_options);
                    charts.push({chart: chart, data: chart_data, options: chart_options});




                    chart_data = new google.visualization.DataTable();
                    chart_data.addColumn("string", "Date");
                    chart_data.addColumn("number", "Average Upload Bandwidth (Mbit/s)");

                    inter_chart_data = [];
                    for (var date in data_month) {
                        if (!data_month.hasOwnProperty(date)) continue;

                        inter_chart_data.push([date, Math.round(data_month[date].upload / data_month[date].dataCount * 100) / 100]);
                    }

                    chart_data.addRows(inter_chart_data);

                    // Set chart options
                    chart_options = {
                        title: "Average Internet Upload Bandwidth Over Past Month",
                        backgroundColor: {fill: "transparent"},
                        chartArea: {width: "75%"},
                        legend: "none",
                        series: {0: {color: "#e7711b"}},
                        hAxis: {
                            title: "Date",
                            titleTextStyle: {
                                bold: true,
                                italic: false
                            }
                        },
                        vAxis: {
                            title: "Bandwidth (Mbit/s)",
                            titleTextStyle: {
                                bold: true,
                                italic: false
                            },
                            minValue: 0
                        }
                    };

                    // Instantiate and draw our chart, passing in some options.
                    chart = new google.visualization.AreaChart(document.getElementById("chart-bandwidth-up-month"));
                    chart.draw(chart_data, chart_options);
                    charts.push({chart: chart, data: chart_data, options: chart_options});




                    chart_data = new google.visualization.DataTable();
                    chart_data.addColumn("string", "Date");
                    chart_data.addColumn("number", "Average Latency (ms)");

                    inter_chart_data = [];
                    for (var date in data_month) {
                        if (!data_month.hasOwnProperty(date)) continue;

                        inter_chart_data.push([date, Math.round(data_month[date].latency / data_month[date].dataCount * 1000) / 1000]);
                    }

                    chart_data.addRows(inter_chart_data);

                    // Set chart options
                    chart_options = {
                        title: "Average Internet Latency Over Past Month",
                        backgroundColor: {fill: "transparent"},
                        chartArea: {width: "75%"},
                        legend: "none",
                        series: {0: {color: "#f1ca3a"}},
                        hAxis: {
                            title: "Date",
                            titleTextStyle: {
                                bold: true,
                                italic: false
                            }
                        },
                        vAxis: {
                            title: "Latency (ms)",
                            titleTextStyle: {
                                bold: true,
                                italic: false
                            },
                            minValue: 0
                        }
                    };

                    // Instantiate and draw our chart, passing in some options.
                    chart = new google.visualization.AreaChart(document.getElementById("chart-latency-month"));
                    chart.draw(chart_data, chart_options);
                    charts.push({chart: chart, data: chart_data, options: chart_options});
                });
            }else{
                alert("Error loading month data: " + res.message);
            }
        }, "json");
    });
</script>
