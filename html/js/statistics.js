$(function () {
    var ctx = $("myChart0");
    var myLineChart = new Chart(
        ctx, {
        type: "line",
        data: {
            datasets: [{
                label: "Temperature of Device1",
                fill: false,
                data: [{
                    x: new Date("2020-11-12 06:01:07"),
                    y: 79.9
                }, {
                    x: new Date("2020-11-12 08:15:12"),
                    y: 30.0
                }, {
                    x: new Date("2020-11-12 09:35:12"),
                    y: 48.1
                }, {
                    x: new Date("2020-11-12 11:09:12"),
                    y: 55.7
                }],
                borderColor: "rgb(0,122,204)",
                borderWidth: 4
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: "Hourly Temperature"
            },
            scales: {
                xAxes: [{
                    type: "time",
                    display: true,
                    displayFormats: {
                        quarter: "hA"
                    },
                    distribution: "series",
                    time: {
                        unit: "minute",
                        stepSize: 2
                    },
                    scaleLabel: {
                        display: true,
                        labelString: "Time"
                    },
                    ticks: {
                        major: {
                            fontStyle: "bold",
                            fontColor: "#FF0000"
                        }
                    }
                }],
                yAxes: [{

                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: "Temperature (°C)"
                    },
                    ticks: {
                        min: 0,
                        max: 100,
                    }
                }]
            }
        }
    });

    var ctx2 = document.getElementById("myChart1");
    var myLineChart2 = new Chart(ctx2, {
        type: "line",
        data: {
            datasets: [{
                label: "Logic of Device1",
                data: [{
                    x: new Date(2020, 11, 10, 4, 15, 12),
                    y: 1
                }, {
                    x: new Date(2020, 11, 10, 5, 35, 54),
                    y: 1
                }, {
                    x: new Date(2020, 11, 10, 7, 25, 32),
                    y: 0
                }, {
                    x: new Date(2020, 11, 10, 8, 43, 9),
                    y: 0
                }, {
                    x: new Date(2020, 11, 10, 9, 10, 53),
                    y: 1
                }, {
                    x: new Date(2020, 11, 10, 10, 30, 32),
                    y: 1
                }],
                fill: "origin",
                borderColor: "rgb(0,122,204)",
                borderWidth: 3,
                backgroundColor: "rgb(0,122,204)",
                pointStyle: "dash",
                steppedLine: true
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: "Hourly Logic of Door?"
            },
            scales: {
                xAxes: [{
                    type: "time",
                    display: true,
                    displayFormats: {
                        quarter: "hA"
                    },
                    distribution: "series",
                    time: {
                        unit: "minute",
                        stepSize: 10
                    },
                    scaleLabel: {
                        display: true,
                        labelString: "Time"
                    },
                    ticks: {
                        major: {
                            fontStyle: "bold",
                            fontColor: "#FF0000"
                        }
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: "Logic"
                    },
                    ticks: {
                        min: 0,
                        max: 1,
                        stepSize: 1
                    }
                }]
            }
        }
    });

});