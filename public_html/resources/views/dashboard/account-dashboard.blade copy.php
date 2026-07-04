@extends('layouts.admin')
{{-- @section('page-title')
    {{ __('Dashboard') }}
@endsection --}}
@push('script-page')
    <script>
        @if (\Auth::user()->can('show account dashboard'))

            // (function() {
            //     var chartBarOptions = {
            //         series: [{

            //                 name: "{{ __('Income') }}",
            //                 data: {!! json_encode($incExpLineChartData['income']) !!}
            //             },
            //             {
            //                 name: "{{ __('Expense') }}",
            //                 data: {!! json_encode($incExpLineChartData['expense']) !!}
            //         }],

            //         chart: {
            //             height: 250,
            //             type: 'area',
            //             // type: 'line',
            //             dropShadow: {
            //                 enabled: true,
            //                 color: '#000',
            //                 top: 18,
            //                 left: 7,
            //                 blur: 10,
            //                 opacity: 0.2
            //             },
            //             toolbar: {
            //                 show: false
            //             }
            //         },
            //         dataLabels: {
            //             enabled: false
            //         },
            //         stroke: {
            //             width: 2,
            //             curve: 'smooth'
            //         },
            //         title: {
            //             text: '',
            //             align: 'left'
            //         },
            //         xaxis: {
            //             categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            //             title: 'Jan',
            //             // categories: {!! json_encode($incExpLineChartData['day']) !!},
            //             // title: {
            //             //     text: '{{ __('Date') }}'
            //             // }
            //         },
            //         // colors: ['#6fd944', '#ff3a6e'],


            //         grid: {
            //             strokeDashArray: 4,
            //         },
            //         legend: {
            //             show: false,
            //         },
            //         markers: {
            //             size: 4,
            //             colors: ['#6fd944', '#FF3A6E'],
            //             opacity: 0.9,
            //             strokeWidth: 2,
            //             hover: {
            //                 size: 7,
            //             }
            //         },
            //         yaxis: {
            //             title: {
            //                 text: '{{ __('Amount') }}'
            //             },

            //         }

            //     };
            //     var arChart = new ApexCharts(document.querySelector("#cash-flow"), chartBarOptions);
            //     arChart.render();
            // })();
            (function() {
                var options = {
                    chart: {
                        height: 300,
                        type: 'bar',
                        toolbar: {
                            show: false,
                        },

                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 2,
                        // curve: 'smooth',
                        show: true,
                        color: ['transparent'],
                    },
                    series: [{
                        name: "{{ __('Income') }}",
                        data: {!! json_encode($incExpBarChartData['income']) !!}
                    }, {
                        name: "{{ __('Expense') }}",
                        data: {!! json_encode($incExpBarChartData['expense']) !!}
                    }],
                    xaxis: {
                        categories: {!! json_encode($incExpBarChartData['month']) !!},
                        labels: {
                            style: {
                                colors: '#6c757d',
                                fontSize: '12px',
                            },
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#6c757d',
                                fontSize: '12px',
                            },
                        },
                        min: 0,

                    },
                    colors: ['#3ec9d6', '#485067'],
                    fill: {
                        type: 'solid',
                    },
                    grid: {
                        strokeDashArray: 4,
                        borderColor: '#e9ecef'
                    },
                    legend: {
                        show: true,
                        position: 'top',
                        horizontalAlign: 'right',
                        labels: {
                            colors: '#495057',
                            useSeriesColors: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '20%',
                            endingShape: 'rounded',
                            columnHeight: '50%',
                            borderRadius: 6, // Apply rounded edges
                            borderRadiusApplication: 'end', // Ensure the rounding is applied correctly
                            barGap: 5, // Simulated gap between bars
                            distributed: false, // Uniform styling for all bars
                        }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#incExpBarChart"), options);
                chart.render();
            })();



            // (function() {

            //     var options = {
            //         chart: {
            //             height: 180,
            //             type: 'bar',
            //             toolbar: {
            //                 show: false,
            //             },
            //         },
            //         dataLabels: {
            //             enabled: false
            //         },
            //         stroke: {
            //             width: 2,
            //             curve: 'smooth'
            //         },
            //         series: [{
            //             name: "{{ __('Income') }}",
            //             data: {!! json_encode($incExpBarChartData['income']) !!}
            //         }, {
            //             name: "{{ __('Expense') }}",
            //             data: {!! json_encode($incExpBarChartData['expense']) !!}
            //         }],
            //         xaxis: {
            //             categories: {!! json_encode($incExpBarChartData['month']) !!},
            //         },
            //         colors: ['#3ec9d6', '#FF3A6E'],
            //         fill: {
            //             type: 'solid',
            //         },
            //         grid: {
            //             strokeDashArray: 4,
            //         },
            //         legend: {
            //             show: true,
            //             position: 'top',
            //             horizontalAlign: 'right',
            //         },
            //         // markers: {
            //         //     size: 4,
            //         //     colors:  ['#3ec9d6', '#FF3A6E',],
            //         //     opacity: 0.9,
            //         //     strokeWidth: 2,
            //         //     hover: {
            //         //         size: 7,
            //         //     }
            //         // }
            //     };
            //     var chart = new ApexCharts(document.querySelector("#incExpBarChart"), options);
            //     chart.render();
            // })();

            // (function() {
            //     var options = {
            //         chart: {
            //             height: 140,
            //             type: 'donut',
            //         },
            //         dataLabels: {
            //             enabled: false,
            //         },
            //         plotOptions: {
            //             pie: {
            //                 donut: {
            //                     size: '70%',
            //                 }
            //             }
            //         },
            //         series: {!! json_encode($expenseCatAmount) !!},
            //         colors: {!! json_encode($expenseCategoryColor) !!},
            //         labels: {!! json_encode($expenseCategory) !!},
            //         legend: {
            //             show: true
            //         }
            //     };
            //     var chart = new ApexCharts(document.querySelector("#expenseByCategory"), options);
            //     chart.render();
            // })();

            // (function() {
            //     var options = {
            //         chart: {
            //             height: 140,
            //             type: 'donut',
            //         },
            //         dataLabels: {
            //             enabled: false,
            //         },
            //         plotOptions: {
            //             pie: {
            //                 donut: {
            //                     size: '70%',
            //                 }
            //             }
            //         },
            //         series: {!! json_encode($incomeCatAmount) !!},
            //         colors: {!! json_encode($incomeCategoryColor) !!},
            //         labels: {!! json_encode($incomeCategory) !!},
            //         legend: {
            //             show: true
            //         }
            //     };
            //     var chart = new ApexCharts(document.querySelector("#incomeByCategory"), options);
            //     chart.render();
            // })();

            // (function() {
            //     var options = {
            //         series: [{{ round($storage_limit, 2) }}],
            //         chart: {
            //             height: 350,
            //             type: 'radialBar',
            //             offsetY: -20,
            //             sparkline: {
            //                 enabled: true
            //             }
            //         },
            //         plotOptions: {
            //             radialBar: {
            //                 startAngle: -90,
            //                 endAngle: 90,
            //                 track: {
            //                     background: "#e7e7e7",
            //                     strokeWidth: '97%',
            //                     margin: 5, // margin is in pixels
            //                 },
            //                 dataLabels: {
            //                     name: {
            //                         show: true
            //                     },
            //                     value: {
            //                         offsetY: -50,
            //                         fontSize: '20px'
            //                     }
            //                 }
            //             }
            //         },
            //         grid: {
            //             padding: {
            //                 top: -10
            //             }
            //         },
            //         colors: ["#6FD943"],
            //         labels: ['Used'],
            //     };
            //     var chart = new ApexCharts(document.querySelector("#limit-chart"), options);
            //     chart.render();
            // })();
            (function() {
                var options = {
                    series: [{{ round($storage_limit, 2) }}], // Adjust the value dynamically
                    chart: {
                        height: 250, // Adjust height to match the compact size
                        type: 'radialBar',
                        offsetY: 0, // Adjust positioning
                    },
                    plotOptions: {
                        radialBar: {
                            startAngle: -135, // Full arc (half circle)
                            endAngle: 135,
                            hollow: {
                                margin: 10,
                                size: '70%', // Control the thickness of the circle
                                background: 'transparent',
                                image: undefined,
                            },
                            track: {
                                background: '#e7e7e7', // Background color for track
                                strokeWidth: '97%',
                                margin: 5, // Margin between the graph and track
                            },
                            dataLabels: {
                                showOn: 'always',
                                name: {
                                    offsetY: -20, // Adjust the vertical alignment
                                    show: true,
                                    color: '#888',
                                    fontSize: '17px',
                                },
                                value: {
                                    formatter: function(val) {
                                        return val + " mb"; // Add units for display (e.g., mb)
                                    },
                                    color: '#111',
                                    fontSize: '22px',
                                    show: true,
                                    offsetY: 10, // Adjust the position
                                }
                            }
                        }
                    },
                    fill: {
                        colors: ["#3ec9d6"], // Set the color for the graph (light blue)
                    },
                    stroke: {
                        lineCap: 'round' // This gives the rounded edges to the circular chart
                    },
                    labels: ['Used'], // Label for the graph
                };

                var chart = new ApexCharts(document.querySelector("#limit-chart"), options);
                chart.render();
            })();

            (function() {
                var options = {
                    series: [{{ round($storage_limit, 2) }}], // Adjust the value dynamically
                    chart: {
                        height: 250, // Adjust height to match the compact size
                        type: 'radialBar',
                        offsetY: 0, // Adjust positioning
                    },
                    plotOptions: {
                        radialBar: {
                            startAngle: -135, // Full arc (half circle)
                            endAngle: 135,
                            hollow: {
                                margin: 10,
                                size: '70%', // Control the thickness of the circle
                                background: 'transparent',
                                image: undefined,
                            },
                            track: {
                                background: '#e7e7e7', // Background color for track
                                strokeWidth: '97%',
                                margin: 5, // Margin between the graph and track
                            },
                            dataLabels: {
                                showOn: 'always',
                                name: {
                                    offsetY: -20, // Adjust the vertical alignment
                                    show: true,
                                    color: '#888',
                                    fontSize: '17px',
                                },
                                value: {
                                    formatter: function(val) {
                                        return val + " %"; // Add units for display (e.g., mb)
                                    },
                                    color: '#111',
                                    fontSize: '22px',
                                    show: true,
                                    offsetY: 10, // Adjust the position
                                }
                            }
                        }
                    },
                    fill: {
                        colors: ["#3ec9d6"], // Set the color for the graph (light blue)
                    },
                    stroke: {
                        lineCap: 'round' // This gives the rounded edges to the circular chart
                    },
                    labels: ['Used'], // Label for the graph
                };

                var chart = new ApexCharts(document.querySelector("#limit-chart2"), options);
                chart.render();
            })();
            (function() {
                // Dummy data for the line graph
                var options = {
                    series: [{
                        name: 'Sample Data',
                        data: [10, 15, 25, 30, 40, 50, 60] // Dummy data points
                    }],
                    chart: {
                        height: 350,
                        type: 'line', // Specify line chart type
                    },
                    stroke: {
                        curve: 'smooth', // Smooth curve for the line
                        width: 2
                    },
                    title: {
                        text: 'Line Graph Example',
                        align: 'left'
                    },
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'], // X-axis categories
                    },
                    yaxis: {
                        title: {
                            text: 'Values'
                        }
                    },
                    fill: {
                        colors: ['#3ec9d6'], // Line color
                        opacity: 1,
                    },
                    markers: {
                        size: 5, // Size of the data points
                        colors: ['#3ec9d6'], // Marker color
                    }
                };

                var chart = new ApexCharts(document.querySelector("#line-chart"), options);
                chart.render();
            })();
        @endif
    </script>

    <!-- Import ECharts Library -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script> --}}


    <!-- Import ECharts Library -->
    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    <script>
        const usedColor = getComputedStyle(document.body).getPropertyValue('--used-color').trim();
        const usedColorLight = getComputedStyle(document.body).getPropertyValue('--used-color-light').trim();
        const usedColorMedium = getComputedStyle(document.body).getPropertyValue('--used-color-medium').trim();
        const usedColorDark = getComputedStyle(document.body).getPropertyValue('--used-color-dark').trim();
        const usedColorDarker = getComputedStyle(document.body).getPropertyValue('--used-color-darker').trim();
        const usedColorContrast = getComputedStyle(document.body).getPropertyValue('--used-color-contrast').trim();
        var customColors = [usedColor, usedColorLight, usedColorMedium, usedColorDark, usedColorDarker, usedColorContrast];
        var remainingGradient = {
            type: 'linear',
            x: 0,
            y: 0,
            x2: 1,
            y2: 1,
            colorStops: [{
                    offset: 0,
                    color: '#eaebee'
                }, // Start color
                {
                    offset: 1,
                    color: '#fefefe'
                } // End color
            ]
        };
        var darkgradient = {
            type: 'linear',
            x: 0,
            y: 0,
            x2: 1,
            y2: 1,
            colorStops: [{
                    offset: 0,
                    color: '#292a33'
                }, // Start color
                {
                    offset: 1,
                    color: '#fefefe'
                } // End color
            ]
        };
    </script>
    {{-- check for dark mode --}}
    <script>
        function isDarkMode() {
            const linkElements = document.querySelectorAll('link[rel="stylesheet"]');
            const basePath = `${window.location.origin}`;
            const darkModeCSSPath = `${basePath}/assets/css/style-dark.css`;

            for (let link of linkElements) {
                if (link.href === darkModeCSSPath) {
                    return true; // Dark mode is enabled
                }
            } else if (link.id = 'main-style') {
                return false;
            }

        }
    </script>
    <script>
        window.onload = function() {
            // Initialize the bar chart
            var darkmode = isDarkMode();
            // // Prepare the dataset from Laravel controller
            // const incExpBarChartData = {!! json_encode($incExpBarChartData) !!};
            const incExpBarChartData = {
                month: [
                    "January",
                    "February",
                    "March",
                    "April",
                    "May",
                    "June",
                    "July",
                    "August",
                    "September",
                    "October",
                    "November",
                    "December"
                ],
                income: [
                    "120034",
                    "154600",
                    "110000",
                    "12300",
                    "40000",
                    "12500",
                    "147803.00",
                    "37621.27",
                    "151.80",
                    "151.80",
                    "51.80",
                    "951.80",

                ],
                expense: [
                    "2500",
                    "65000",
                    "98000",
                    "1600",
                    "15000",
                    "56000",
                    "65000",
                    "27952.00",
                    "6577",
                    "9087",
                    "0988",
                    "15678"
                ]
            };

            // console.log(incExpBarChartData);

            var barChartDom = document.getElementById('main123456789');
            var barChart = echarts.init(barChartDom);

            // Bar chart options
            var barOption = {
                legend: {
                    data: ['Income', 'Expense']
                },
                tooltip: {},
                dataset: {
                    dimensions: ['month', 'income', 'expense'],
                    source: incExpBarChartData.month.map((month, index) => {
                        return {
                            month: month,
                            income: parseFloat(incExpBarChartData.income[index]) || 0,
                            expense: parseFloat(incExpBarChartData.expense[index]) || 0
                        };
                    })
                },
                xAxis: {
                    type: 'category',
                    axisLine: {
                        show: false
                    },
                    splitLine: {
                        show: false
                    },
                    axisTick: {
                        show: false
                    },
                    axisLabel: {
                        show: true
                    }
                },
                yAxis: {
                    splitLine: {
                        show: false
                    }
                },
                series: [{
                        type: 'bar',
                        barWidth: '20%',
                        itemStyle: {
                            borderRadius: [10, 10, 10, 10],
                            color: usedColor,
                        }
                    },
                    {
                        type: 'bar',
                        barWidth: '20%',
                        itemStyle: {
                            borderRadius: [10, 10, 10, 10],
                            color: usedColorLight,
                        }
                    }
                ]
            };

            // Set the option to the bar chart instance
            barChart.setOption(barOption);
            window.onresize = function() {
                barChart.resize();
                barChartDom.resize();
            }
            var pieChartDom = document.getElementById('limit-chart213');
            var pieChartDom2 = document.getElementById('limit-chart123');

            var pieChart = echarts.init(pieChartDom);
            var pieChart2 = echarts.init(pieChartDom2);

            window.onresize = function() {
                pieChart.resize();
                pieChart2.resize();
            }
            // Example total storage in MB
            const totalStorage = 100; 
            const usedStorage = {{ round(58, 2) }}; 
            const remainingStorage = totalStorage - usedStorage;


            var pieOption = {
                tooltip: { // Disable tooltips
                    show: false
                },
                legend: {
                    top: '5%',
                    left: '0%',
                    textStyle: {
                        color: isDarkMode() ? '#fff' : '#000', // Dynamic text color based on theme
                        fontSize: 14, // Customize font size
                        fontWeight: 'bold', // Customize font weight
                        fontFamily: 'Arial', // Customize font family
                    }
                },
                series: [{
                    name: 'Storage Usage',
                    type: 'pie',
                    radius: ['55%', '75%'], // Thinner ring
                    avoidLabelOverlap: true,
                    itemStyle: {
                        borderRadius: 10,
                        borderColor: isDarkMode() ? '#292a33' : '#fff',
                        borderWidth: 2,
                    },
                    label: {
                        show: true,
                        position: 'center', // Center the label
                        formatter: '{b}: {c} MB',
                        fontSize: 16, // Custom font size
                        fontWeight: 'normal', // Custom font weight
                        color: isDarkMode() ? '#fff' : '#000', // Dynamic text color based on theme
                        textBorderColor: isDarkMode() ? '#292a33' :
                        '#fff', // Optional: Add a text border color
                        textBorderWidth: 2, // Optional: Add a text border width
                    },
                    emphasis: {
                        label: {
                            show: true,
                            fontSize: '14px',
                            fontWeight: 'normal'
                        }
                    },
                    labelLine: {
                        show: true
                    },
                    data: [{
                            value: usedStorage,
                            name: 'Used',
                            itemStyle: {
                                color: usedColor
                            } // Use dynamic color for 'Used' segment
                        },
                        {
                            value: remainingStorage,
                            name: 'Remaining',
                            itemStyle: {
                                color: isDarkMode() ? darkgradient : remainingGradient,
                            } // Use dynamic color for 'Remaining' segment
                        }
                    ]
                }]
            };

            // Pie chart options for showing percentages
            var pieOption2 = {
                tooltip: { // Disable tooltips
                    show: false
                },
                legend: {
                    top: '5%',
                    right: '0%',
                    textStyle: {
                        color: isDarkMode() ? '#fff' : '#000', // Dynamic text color based on theme
                        fontSize: 14, // Customize font size
                        fontWeight: 'bold', // Customize font weight
                        fontFamily: 'Arial', // Customize font family
                    }
                },
                series: [{
                    name: 'Storage Usage',
                    type: 'pie',
                    radius: ['55%', '75%'], // Thinner ring
                    avoidLabelOverlap: true,
                    itemStyle: {
                        borderRadius: 10,
                        borderColor: isDarkMode() ? '#292a33' : '#fff',
                        borderWidth: 2
                    },
                    label: {
                        show: true,
                        position: 'center', // Center the label
                        formatter: '{b}: {c}%',
                        fontSize: 16, // Custom font size
                        fontWeight: 'normal', // Custom font weight
                        color: isDarkMode() ? '#fff' : '#000', // Dynamic text color based on theme
                        textBorderColor: isDarkMode() ? '#292a33' :
                        '#fff', // Optional: Add a text border color
                        textBorderWidth: 2, // Optional: Add a text border width
                    },
                    emphasis: {
                        label: {
                            show: true,
                            fontSize: '16px',
                            fontWeight: 'normal'
                        }
                    },
                    labelLine: {
                        show: true
                    },
                    data: [{
                            value: usedStorage,
                            name: 'Used',
                            itemStyle: {
                                color: usedColor
                            } // Use dynamic color for 'Used' segment
                        },
                        {
                            value: remainingStorage,
                            name: 'Remaining',
                            itemStyle: {
                                color: isDarkMode() ? darkgradient : remainingGradient,
                            } // Use dynamic color for 'Remaining' segment
                        }
                    ]
                }]
            };

            // Set the options to the pie chart instances
            pieChart.setOption(pieOption);
            pieChart2.setOption(pieOption2);


            // var incExpLineChartData = @json($incExpLineChartData); // just to visualize the graph i am putting dummy data 
            var incExpLineChartData = {
                day: [
                    "27-Sep", "26-Sep", "25-Sep", "24-Sep", "23-Sep",
                    "22-Sep", "21-Sep", "20-Sep", "19-Sep", "18-Sep",
                    "17-Sep", "16-Sep", "15-Sep", "14-Sep", "13-Sep"
                ],
                income: [100.00, 150.00, 200.00, 300.00, 250.00, 400.00, 350.00, 0.00, 0.00, 0.00, 50.00,
                    100.00,
                    150.00, 200.00, 250.00
                ],
                expense: [80.00, 90.00, 70.00, 120.00, 110.00, 160.00, 140.00, 50.00, 0.00, 0.00, 30.00,
                    70.00,
                    80.00, 90.00, 100.00
                ]
            };
            var linechartDom = document.getElementById('line_chart_123');
            var lineChart = echarts.init(linechartDom);
            var option;

            setTimeout(function() {
                option = {
                    legend: {
                        textStyle: {
                            color: isDarkMode() ? '#fff' : '#000', // Dynamic text color based on theme
                            fontSize: 14, // Customize font size
                            fontWeight: 'bold', // Customize font weight
                            fontFamily: 'Arial', // Customize font family
                        }
                    },
                    tooltip: {
                        trigger: 'axis',
                        showContent: false
                    },

                    xAxis: {
                        type: 'category',
                        data: incExpLineChartData.day,
                        axisLine: {
                            show: false
                        },
                        splitLine: {
                            show: false
                        },
                        axisTick: {
                            show: false
                        },
                        axisLabel: {
                            show: true
                        }

                    },
                    yAxis: {
                        gridIndex: 0,
                        splitLine: {
                            show: false // Disable grid lines
                        }
                    },
                    grid: {
                        top: '55%'
                    },
                    series: [{
                            name: 'Income',
                            type: 'line',
                            data: incExpLineChartData.income.map(
                                Number), // Convert income strings to numbers
                            smooth: true,
                            itemStyle: {
                                color: usedColorLight,
                            }
                        },
                        {
                            name: 'Expense',
                            type: 'line',
                            data: incExpLineChartData.expense.map(
                                Number), // Convert expense strings to numbers
                            smooth: true,
                            itemStyle: {
                                color: usedColor,
                            }
                        }
                    ]
                };

                lineChart.setOption(option);
            });

            option && myChart.setOption(option);


            //weekly-monthly chart
            var barChartDom2 = document.getElementById('barchart_weekly_monthly');
            var barChart2 = echarts.init(barChartDom2);
            // Bar chart options
            var barOption = {
                legend: {
                    textStyle: {
                        color: isDarkMode() ? '#fff' : '#000', // Dynamic text color based on theme
                        fontSize: 14, // Customize font size
                        fontWeight: 'bold', // Customize font weight
                        fontFamily: 'Arial', // Customize font family
                    }
                },
                tooltip: {},
                dataset: {
                    dimensions: ['month', 'income', 'expense'],
                    source: incExpBarChartData.month.map((month, index) => {
                        return {
                            month: month,
                            income: parseFloat(incExpBarChartData.income[index]) || 0,
                            expense: parseFloat(incExpBarChartData.expense[index]) || 0
                        };
                    })
                },
                xAxis: {
                    type: 'category',
                    axisLine: {
                        show: false
                    },
                    splitLine: {
                        show: false
                    },
                    axisTick: {
                        show: false
                    },
                    axisLabel: {
                        show: true
                    }
                },
                yAxis: {
                    splitLine: {
                        show: false
                    }
                },
                series: [{
                        type: 'bar',
                        barWidth: '20%',
                        itemStyle: {
                            borderRadius: [10, 10, 10, 10],
                            color: usedColor,
                        }
                    },
                    {
                        type: 'bar',
                        barWidth: '20%',
                        itemStyle: {
                            borderRadius: [10, 10, 10, 10],
                            color: usedColorLight,
                        }
                    }
                ]
            };

            // Prepare the data from Laravel
            var series = {!! json_encode($incomeCatAmount) !!};
            var colors = {!! json_encode($incomeCategoryColor) !!};
            var labels = {!! json_encode($incomeCategory) !!};

            // Prepare the pie chart data
            var pieData = labels.map((label, index) => ({
                value: series[index],
                name: label
            }));

            var incomeCategoryChartDom = document.getElementById('incomeCategoryChart');
            var incomeCategoryChart = echarts.init(incomeCategoryChartDom);

            // Pie chart options
            var option = {
                title: {
                    text: 'Income by Category',
                    left: 'center'
                },
                tooltip: {
                    trigger: 'item'
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data: labels, // Use category labels for the legend
                    textStyle: {
                        color: isDarkMode() ? '#fff' : '#000', // Dynamic text color based on theme
                        fontSize: 14, // Customize font size
                        fontWeight: 'bold', // Customize font weight
                        fontFamily: 'Arial', // Customize font family
                    }
                },
                series: [{
                    name: 'Income Categories',
                    type: 'pie',
                    radius: '50%',
                    data: pieData,
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    },
                    itemStyle: {
                        color: function(params) {
                            // Use the colors array to get the color for each section
                            return colors[params.dataIndex];
                        }
                    },
                    label: {
                        formatter: '{b}: {d}%' // Show category name and percentage
                    }
                }]
            };





        };
    </script>

    {{-- <script>
        // Sample data structure from your PHP code
        var invoiceData = @json($weeklyInvoice);

        // // Prepare the data for the bar chart
        // var dates = Object.keys(invoiceData);
        // var invoiceTotals = dates.map(date => invoiceData[date].invoiceTotal);
        // var invoicePaid = dates.map(date => invoiceData[date].invoicePaid);
        // var invoiceDue = dates.map(date => invoiceData[date].invoiceDue);


        // Preparing the data for the bar chart
        var dates = Object.keys(invoiceData);
        var invoiceTotals = dates.map(date => invoiceData[date].invoiceTotal);
        var invoicePaid = dates.map(date => invoiceData[date].invoicePaid);
        var invoiceDue = dates.map(date => invoiceData[date].invoiceDue);

        var barOption = {
            legend: {
                data: ['Total', 'Paid', 'Due']
            },
            tooltip: {},
            dataset: {
                dimensions: ['date', 'Total', 'Paid', 'Due'],
                source: dates.map((date, index) => {
                    return {
                        date: date,
                        Total: parseFloat(invoiceTotals[index]) || 0,
                        Paid: parseFloat(invoicePaid[index]) || 0,
                        Due: parseFloat(invoiceDue[index]) || 0
                    };
                })
            },
            xAxis: {
                type: 'category',
                axisLine: {
                    show: false
                },
                splitLine: {
                    show: false
                },
                axisTick: {
                    show: false
                },
                axisLabel: {
                    show: true
                }
            },
            yAxis: {
                splitLine: {
                    show: false
                }
            },
            series: [{
                    type: 'bar',
                    barWidth: '20%',
                    itemStyle: {
                        borderRadius: [10, 10, 10, 10],
                        color: usedColor, // Replace with your usedColor
                    }
                },
                {
                    type: 'bar',
                    barWidth: '20%',
                    itemStyle: {
                        borderRadius: [0, 0, 10, 10],
                        color: usedColorMedium, // Replace with your usedColorLight
                    }
                },
                {
                    type: 'bar',
                    barWidth: '20%',
                    itemStyle: {
                        borderRadius: [10, 10, 10, 10],
                        color: usedColorLight, // Color for the 'Due' amount
                    }
                }
            ]
        };

        // Initialize ECharts instance
        var myChart = echarts.init(document.getElementById('weeklyChart'));
        myChart.setOption(barOption);
    </script>
    <script>
        // Get the monthly invoice data from the PHP variable
        var invoiceData = @json($monthlyInvoice);
        // Prepare the data for the bar chart
        var dates = Object.keys(invoiceData);
        var invoiceTotals = dates.map(date => invoiceData[date].invoiceTotal);
        var invoicePaid = dates.map(date => invoiceData[date].invoicePaid);
        var invoiceDue = dates.map(date => invoiceData[date].invoiceDue);
        console.log("Dates: ", dates);
        console.log("Totals: ", invoiceTotals);
        console.log("Paid: ", invoicePaid);
        console.log("Due: ", invoiceDue);
        var monthlyBarOption = {
            legend: {
                data: ['Total', 'Paid', 'Due']
            },
            tooltip: {},
            dataset: {
                dimensions: ['date', 'Total', 'Paid', 'Due'],
                source: dates.map((date, index) => {
                    return {
                        date: date,
                        Total: parseFloat(invoiceTotals[index]) || 0,
                        Paid: parseFloat(invoicePaid[index]) || 0,
                        Due: parseFloat(invoiceDue[index]) || 0
                    };
                })
            },
            xAxis: {
                type: 'category',
                data: dates, // This should include all your dates

                axisLine: {
                    show: false
                },
                splitLine: {
                    show: false
                },
                axisTick: {
                    show: false
                },
                axisLabel: {
                    show: true
                }
            },
            yAxis: {
                splitLine: {
                    show: false
                }
            },
            series: [{
                    type: 'bar',
                    barWidth: '20%',
                    itemStyle: {
                        borderRadius: [10, 10, 10, 10],
                        color: usedColor, // Total color
                    }
                },
                {
                    type: 'bar',
                    barWidth: '20%',
                    itemStyle: {
                        borderRadius: [0, 0, 10, 10],
                        color: usedColorMedium, // Paid color
                    }
                },
                {
                    type: 'bar',
                    barWidth: '20%',
                    itemStyle: {
                        borderRadius: [10, 10, 10, 10],
                        color: usedColorLight, // Due color
                    }
                }
            ]
        };

        // Initialize ECharts instance
        var myMonthlyChart = echarts.init(document.getElementById('monthlyChart'));
        myMonthlyChart.setOption(monthlyBarOption);
    </script>
    <script>
        // Fetch yearly invoice data from Laravel
        var invoiceData = @json($yearlyInvoice);

        // Prepare the dataset for the chart
        var sourceData = Object.keys(invoiceData).map(function(month) {
            return {
                month: month,
                total: invoiceData[month].invoiceTotal,
                paid: invoiceData[month].invoicePaid,
                due: invoiceData[month].invoiceDue
            };
        });

        // Set up yearlyData using the sourceData
        var yearlyData = {
            legend: {
                data: ['Total', 'Paid', 'Due']
            },
            tooltip: {
                trigger: 'item'
            },
            dataset: {
                dimensions: ['month', 'total', 'paid', 'due'],
                source: sourceData
            },
            xAxis: {
                type: 'category',
                axisLine: {
                    show: false
                },
                splitLine: {
                    show: false
                },
                axisTick: {
                    show: false
                },
                axisLabel: {
                    show: true
                }
            },
            yAxis: {
                splitLine: {
                    show: false
                }
            },
            series: [{
                    name: 'Total',
                    type: 'bar',
                    barWidth: '20%',
                    itemStyle: {
                        borderRadius: [10, 10, 10, 10],
                        color: usedColor // Green for Total
                    }
                },
                {
                    name: 'Paid',
                    type: 'bar',
                    barWidth: '20%',
                    itemStyle: {
                        borderRadius: [10, 10, 10, 10],
                        color: usedColorMedium // Blue for Paid
                    }
                },
                {
                    name: 'Due',
                    type: 'bar',
                    barWidth: '20%',
                    itemStyle: {
                        borderRadius: [10, 10, 10, 10],
                        color: usedColorLight // Red for Due
                    }
                }
            ]
        };
        // Initialize ECharts instance
        var myMonthlyChart = echarts.init(document.getElementById('yearlyChart'));
        myMonthlyChart.setOption(yearlyData);
    </script>

    <script>
        function showChart(chartId, btnId) {
            var charts = document.querySelectorAll('.chart'); // Select all charts
            var btn = document.getElementById(btnId);

            // Remove 'active' class from all buttons
            var buttons = document.querySelectorAll('.btn-graph-toggler');
            buttons.forEach(function(button) {
                button.classList.remove('active');
            });

            // Add 'active' class to the clicked button
            btn.classList.add('active');

            // Hide all charts
            charts.forEach(function(chart) {
                chart.style.visibility = 'hidden';
                chart.style.position = 'absolute';
            });

            // Show the selected chart by its ID
            var selectedChart = document.getElementById(chartId);
            if (selectedChart) {
                selectedChart.style.visibility = 'visible';
                selectedChart.style.position = 'relative';
            } else {
                console.error(`Chart with ID "${chartId}" not found.`);
            }
        }
    </script> --}}
    <script>
        // Declare variables to hold the chart instances
        var myChart, myMonthlyChart, myYearlyChart;

        // Function to create weekly chart
        function createWeeklyChart() {

            // var invoiceData = @json($weeklyInvoice);
            var invoiceData = {
                "Sun": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Mon": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Tue": {
                    "invoiceTotal": 100,
                    "invoicePaid": 50,
                    "invoiceDue": 50
                },
                "Wed": {
                    "invoiceTotal": 200,
                    "invoicePaid": 150,
                    "invoiceDue": 50
                },
                "Thu": {
                    "invoiceTotal": 300,
                    "invoicePaid": 200,
                    "invoiceDue": 100
                },
                "Fri": {
                    "invoiceTotal": 400,
                    "invoicePaid": 300,
                    "invoiceDue": 100
                },
                "Sat": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                }
            }

            // console.log(invoiceData);
            var dates = Object.keys(invoiceData);
            var invoiceTotals = dates.map(date => invoiceData[date].invoiceTotal);
            var invoicePaid = dates.map(date => invoiceData[date].invoicePaid);
            var invoiceDue = dates.map(date => invoiceData[date].invoiceDue);

            var barOption = {
                legend: {
                    data: ['Total', 'Paid', 'Due'],
                    textStyle: {
                        color: isDarkMode() ? '#fff' : '#000', // Dynamic text color based on theme
                        fontSize: 14, // Customize font size
                        fontWeight: 'bold', // Customize font weight
                        fontFamily: 'Arial', // Customize font family
                    }
                },
                tooltip: {},
                dataset: {
                    dimensions: ['date', 'Total', 'Paid', 'Due'],
                    source: dates.map((date, index) => ({
                        date: date,
                        Total: parseFloat(invoiceTotals[index]) || 0,
                        Paid: parseFloat(invoicePaid[index]) || 0,
                        Due: parseFloat(invoiceDue[index]) || 0
                    }))
                },
                xAxis: {
                    type: 'category',
                    axisLine: {
                        show: false
                    },
                    splitLine: {
                        show: false
                    },
                    axisTick: {
                        show: false
                    },
                    axisLabel: {
                        show: true
                    }
                },
                yAxis: {
                    splitLine: {
                        show: false
                    }
                },
                series: [{
                        type: 'bar',
                        barWidth: '20%',
                        itemStyle: {
                            borderRadius: [10, 10, 10, 10],
                            color: usedColor
                        }
                    },
                    {
                        type: 'bar',
                        barWidth: '20%',
                        itemStyle: {
                            borderRadius: [10, 10, 10, 10],
                            color: usedColorMedium
                        }
                    },
                    {
                        type: 'bar',
                        barWidth: '20%',
                        itemStyle: {
                            borderRadius: [10, 10, 10, 10],
                            color: usedColorLight
                        }
                    }
                ]
            };

            // Initialize ECharts instance
            if (myChart) myChart.dispose(); // Destroy previous instance if it exists
            myChart = echarts.init(document.getElementById('weeklyChart'));
            myChart.setOption(barOption);
            window.onresize = function() {
                myChart.resize();
            }
        }

        // Function to create monthly chart
        function createMonthlyChart() {
            // var invoiceData = @json($monthlyInvoice);
            // console.log(invoiceData);
            var invoiceData = {
                "Oct-1": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-2": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-3": {
                    "invoiceTotal": 1656.0,
                    "invoicePaid": 0.0,
                    "invoiceDue": 1656.0
                },
                "Oct-4": {
                    "invoiceTotal": 1200.0,
                    "invoicePaid": 800.0,
                    "invoiceDue": 400.0
                },
                "Oct-5": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-6": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-7": {
                    "invoiceTotal": 950.0,
                    "invoicePaid": 950.0,
                    "invoiceDue": 0
                },
                "Oct-8": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-9": {
                    "invoiceTotal": 750.0,
                    "invoicePaid": 500.0,
                    "invoiceDue": 250.0
                },
                "Oct-10": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-11": {
                    "invoiceTotal": 1350.0,
                    "invoicePaid": 650.0,
                    "invoiceDue": 700.0
                },
                "Oct-12": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-13": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-14": {
                    "invoiceTotal": 1850.0,
                    "invoicePaid": 1850.0,
                    "invoiceDue": 0
                },
                "Oct-15": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-16": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-17": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-18": {
                    "invoiceTotal": 2600.0,
                    "invoicePaid": 1000.0,
                    "invoiceDue": 1600.0
                },
                "Oct-19": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-20": {
                    "invoiceTotal": 1450.0,
                    "invoicePaid": 950.0,
                    "invoiceDue": 500.0
                },
                "Oct-21": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-22": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-23": {
                    "invoiceTotal": 3200.0,
                    "invoicePaid": 2200.0,
                    "invoiceDue": 1000.0
                },
                "Oct-24": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-25": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-26": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-27": {
                    "invoiceTotal": 4000.0,
                    "invoicePaid": 3500.0,
                    "invoiceDue": 500.0
                },
                "Oct-28": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-29": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Oct-30": {
                    "invoiceTotal": 5200.0,
                    "invoicePaid": 4200.0,
                    "invoiceDue": 1000.0
                },
                "Oct-31": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                }
            };

            var dates = Object.keys(invoiceData);
            var invoiceTotals = dates.map(date => invoiceData[date].invoiceTotal);
            var invoicePaid = dates.map(date => invoiceData[date].invoicePaid);
            var invoiceDue = dates.map(date => invoiceData[date].invoiceDue);

            var monthlyBarOption = {
                legend: {
                    data: ['Total', 'Paid', 'Due'],
                    textStyle: {
                        color: isDarkMode() ? '#fff' : '#000', // Dynamic text color based on theme
                        fontSize: 14, // Customize font size
                        fontWeight: 'bold', // Customize font weight
                        fontFamily: 'Arial', // Customize font family
                    }
                },
                tooltip: {},
                dataset: {
                    dimensions: ['date', 'Total', 'Paid', 'Due'],
                    source: dates.map((date, index) => ({
                        date: date,
                        Total: parseFloat(invoiceTotals[index]) || 0,
                        Paid: parseFloat(invoicePaid[index]) || 0,
                        Due: parseFloat(invoiceDue[index]) || 0
                    }))
                },
                xAxis: {
                    type: 'category',
                    data: dates,
                    axisLine: {
                        show: false
                    },
                    splitLine: {
                        show: false
                    },
                    axisTick: {
                        show: false
                    },
                    axisLabel: {
                        show: true
                    }
                },
                yAxis: {
                    splitLine: {
                        show: false
                    }
                },
                series: [{
                        type: 'bar',
                        barWidth: '20%',
                        itemStyle: {
                            borderRadius: [10, 10, 10, 10],
                            color: usedColor
                        }
                    },
                    {
                        type: 'bar',
                        barWidth: '20%',
                        itemStyle: {
                            borderRadius: [10, 10, 10, 10],
                            color: usedColorMedium
                        }
                    },
                    {
                        type: 'bar',
                        barWidth: '20%',
                        itemStyle: {
                            borderRadius: [10, 10, 10, 10],
                            color: usedColorLight
                        }
                    }
                ]
            };

            // Initialize ECharts instance
            if (myMonthlyChart) myMonthlyChart.dispose(); // Destroy previous instance if it exists
            myMonthlyChart = echarts.init(document.getElementById('monthlyChart'));
            myMonthlyChart.setOption(monthlyBarOption);
            window.onresize = function() {
                window.onresize = function() {
                    myMonthlyChart.resize();
                }
            };
        }

        // Function to create yearly chart
        function createYearlyChart() {
            // var invoiceData = @json($yearlyInvoice);
            var invoiceData = {
                "jan": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "feb": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Mar": {
                    "invoiceTotal": 1656.0,
                    "invoicePaid": 0.0,
                    "invoiceDue": 1656.0
                },
                "Apr": {
                    "invoiceTotal": 1200.0,
                    "invoicePaid": 800.0,
                    "invoiceDue": 400.0
                },
                "jun": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "july": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "Aug": {
                    "invoiceTotal": 950.0,
                    "invoicePaid": 950.0,
                    "invoiceDue": 0
                },
                "sept": {
                    "invoiceTotal": 1350.0,
                    "invoicePaid": 650.0,
                    "invoiceDue": 700.0
                },
                "oct": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                },
                "nov": {
                    "invoiceTotal": 750.0,
                    "invoicePaid": 500.0,
                    "invoiceDue": 250.0
                },
                "dec": {
                    "invoiceTotal": 0,
                    "invoicePaid": 0,
                    "invoiceDue": 0
                }
            }
            console.log(invoiceData);
            var sourceData = Object.keys(invoiceData).map(month => ({
                month: month,
                total: invoiceData[month].invoiceTotal,
                paid: invoiceData[month].invoicePaid,
                due: invoiceData[month].invoiceDue
            }));

            var yearlyData = {
                legend: {
                    data: ['Total', 'Paid', 'Due'],
                    textStyle: {
                        color: isDarkMode() ? '#fff' : '#000', // Dynamic text color based on theme
                        fontSize: 14, // Customize font size
                        fontWeight: 'bold', // Customize font weight
                        fontFamily: 'Arial', // Customize font family
                    }
                },
                tooltip: {
                    trigger: 'item'
                },
                dataset: {
                    dimensions: ['month', 'total', 'paid', 'due'],
                    source: sourceData
                },
                xAxis: {
                    type: 'category',
                    axisLine: {
                        show: false
                    },
                    splitLine: {
                        show: false
                    },
                    axisTick: {
                        show: false
                    },
                    axisLabel: {
                        show: true
                    }
                },
                yAxis: {
                    splitLine: {
                        show: false
                    }
                },
                series: [{
                        name: 'Total',
                        type: 'bar',
                        barWidth: '20%',
                        itemStyle: {
                            borderRadius: [10, 10, 10, 10],
                            color: usedColor
                        }
                    },
                    {
                        name: 'Paid',
                        type: 'bar',
                        barWidth: '20%',
                        itemStyle: {
                            borderRadius: [10, 10, 10, 10],
                            color: usedColorMedium
                        }
                    },
                    {
                        name: 'Due',
                        type: 'bar',
                        barWidth: '20%',
                        itemStyle: {
                            borderRadius: [10, 10, 10, 10],
                            color: usedColorLight
                        }
                    }
                ]
            };

            // Initialize ECharts instance
            if (myYearlyChart) myYearlyChart.dispose(); // Destroy previous instance if it exists
            myYearlyChart = echarts.init(document.getElementById('yearlyChart'));
            myYearlyChart.setOption(yearlyData);
        }

        // Common function to show the selected chart
        function showChart(chartId, btnId) {
            var charts = document.querySelectorAll('.chart');
            var btn = document.getElementById(btnId);

            // Remove 'active' class from all buttons
            var buttons = document.querySelectorAll('.btn-graph-toggler');
            buttons.forEach(function(button) {
                button.classList.remove('active');
            });

            // Add 'active' class to the clicked button
            btn.classList.add('active');

            // Hide all charts
            charts.forEach(function(chart) {
                chart.style.visibility = 'hidden';
                chart.style.position = 'absolute';
            });

            // Show the selected chart by its ID
            var selectedChart = document.getElementById(chartId);
            if (selectedChart) {
                selectedChart.style.visibility = 'visible';
                selectedChart.style.position = 'relative';

                // Recreate the chart depending on which one is shown
                if (chartId === 'weeklyChart') {
                    createWeeklyChart();
                } else if (chartId === 'monthlyChart') {
                    createMonthlyChart();
                } else if (chartId === 'yearlyChart') {
                    createYearlyChart();
                }
            } else {
                console.error(`Chart with ID "${chartId}" not found.`);
            }
        }

        // Create the initial chart
        createYearlyChart(); // You can choose which chart to show initially
    </script>


    <script>
        // Initialize variables to hold chart instances
        var incomeChart, expenseChart;
        window.onresize = function() {
            incomeChart.resize();
            expenseChart.resize();
        }
        // Income chart data and options
        function initIncomeChart() {
            if (!incomeChart) {
                var incomeCatAmount = {!! json_encode($incomeCatAmount) !!};
                var incomeCategory = {!! json_encode($incomeCategory) !!};
                var data = incomeCategory.map(function(category, index) {
                    return {
                        value: incomeCatAmount[index],
                        name: category,
                        itemStyle: {
                            color: customColors[index % customColors.length]
                        }
                    };
                });

                var pieOption = {
                    tooltip: {
                        show: false
                    },
                    legend: {
                        top: '5%',
                        left: '0%',
                        orient: 'vertical'
                    },
                    series: [{
                        name: 'Income Categories',
                        type: 'pie',
                        radius: ['55%', '75%'],
                        avoidLabelOverlap: true,
                        itemStyle: {
                            borderRadius: 10,
                            borderColor: '#fff',
                            borderWidth: 2
                        },
                        label: {
                            show: false,
                            position: 'center',
                            formatter: '{b}: {c}'
                        },
                        emphasis: {
                            label: {
                                show: true,
                                fontSize: '16px',
                                fontWeight: 'normal'
                            }
                        },
                        labelLine: {
                            show: true
                        },
                        data: data
                    }]
                };

                // Initialize income chart
                incomeChart = echarts.init(document.querySelector("#incomeByCategory"));
                incomeChart.setOption(pieOption);
            }
        }

        // Expense chart data and options
        function initExpenseChart() {
            var incomeChartstyle = document.getElementById('incomeByCategory');
            var expenseChartstyle = document.getElementById('expenseByCategory');
            if (!expenseChart) {
                var expenseData = {!! json_encode($expenseCatAmount) !!};
                var expenseLabels = {!! json_encode($expenseCategory) !!};
                var seriesData = expenseData.map((value, index) => {
                    return {
                        value: value,
                        name: expenseLabels[index],
                        itemStyle: {
                            color: customColors[index % customColors.length]
                        }
                    };
                });

                var options = {
                    tooltip: {
                        show: true,
                        formatter: '{b}: {c}'
                    },
                    legend: {
                        top: '0%',
                        left: '0%',
                        orient: 'vertical'
                    },
                    series: [{
                        name: 'Expenses by Category',
                        type: 'pie',
                        radius: ['55%', '75%'],
                        avoidLabelOverlap: false,
                        itemStyle: {
                            borderRadius: 10,
                            borderColor: '#fff',
                            borderWidth: 2
                        },
                        label: {
                            show: false,
                            position: 'center',
                            formatter: '{b}: {c}'
                        },
                        emphasis: {
                            label: {
                                show: true,
                                fontSize: '22px',
                                fontWeight: 'normal'
                            }
                        },
                        labelLine: {
                            show: true
                        },
                        data: seriesData
                    }]
                };

                // Initialize expense chart
                expenseChart = echarts.init(document.getElementById('expenseByCategory'));
                expenseChart.setOption(options);
            }
        }

        // Destroy income chart
        function destroyIncomeChart() {
            if (incomeChart) {
                incomeChart.dispose();
                incomeChart = null;
            }
        }

        // Destroy expense chart
        function destroyExpenseChart() {
            if (expenseChart) {
                expenseChart.dispose();
                expenseChart = null;
            }
        }

        // Initialize income chart by default
        initIncomeChart();

        // Add event listeners for toggling charts
        document.getElementById('incomeByCategory_h').addEventListener('click', function() {
            destroyExpenseChart();
            console.log('incomeByCategory_h clicked');
            initIncomeChart();
        });

        document.getElementById('expenseByCategory_h').addEventListener('click', function() {
            destroyIncomeChart();
            console.log('expenseByCategory_h clicked');
            initExpenseChart();
        });
    </script>

    <!-- JavaScript for Chart Switching -->



    <script>
        // Initialize variables to hold chart instances
        var currentChart;
        window.onresize = function() {
            currentChart.resize();
        }
        // Income chart data and options
        function initIncomeChart() {
            var incomeCatAmount = {!! json_encode($incomeCatAmount) !!};
            var incomeCategory = {!! json_encode($incomeCategory) !!};
            var data = incomeCategory.map(function(category, index) {
                return {
                    value: incomeCatAmount[index],
                    name: category,
                    itemStyle: {
                        color: customColors[index % customColors.length]
                    }
                };
            });

            var pieOption = {
                tooltip: {
                    show: true,
                    formatter: '{b}: {c}'
                },
                legend: {
                    top: '5%',
                    left: '0%',
                    orient: 'vertical',
                    textStyle: {
                        color: isDarkMode() ? '#fff' : '#000', // Dynamic text color based on theme
                        fontSize: 14, // Customize font size
                        fontWeight: 'bold', // Customize font weight
                        fontFamily: 'Arial', // Customize font family
                    }
                },
                series: [{
                    name: 'Income Categories',
                    type: 'pie',
                    radius: ['55%', '75%'],
                    avoidLabelOverlap: true,
                    itemStyle: {
                        borderRadius: 10,
                        borderColor: isDarkMode() ? '#292a33' : '#fff',
                        borderWidth: 2
                    },
                    label: {
                        show: true,
                        position: 'outside',
                        formatter: '{b}',
                        textStyle: {
                            color: isDarkMode() ? '#fff' : '#000', // Dynamic text color based on theme
                            fontSize: 14, // Customize font size
                            fontWeight: 'bold', // Customize font weight
                            fontFamily: 'Arial', // Customize font family
                        }
                    },
                    emphasis: {
                        label: {
                            show: true,
                            fontSize: '16px',
                            fontWeight: 'normal'
                        }
                    },
                    labelLine: {
                        show: false
                    },
                    data: data
                }]
            };

            // Initialize income chart
            currentChart = echarts.init(document.querySelector("#chartContainer"));
            currentChart.setOption(pieOption);
        }

        // Expense chart data and options
        function initExpenseChart() {
            var expenseData = {!! json_encode($expenseCatAmount) !!};
            var expenseLabels = {!! json_encode($expenseCategory) !!};
            var seriesData = expenseData.map((value, index) => {
                return {
                    value: value,
                    name: expenseLabels[index],
                    itemStyle: {
                        color: customColors[index % customColors.length]
                    }
                };
            });

            var options = {
                tooltip: {
                    show: true,
                    formatter: '{b}: {c}'
                },
                legend: {
                    top: '0%',
                    left: '0%',
                    orient: 'vertical'
                },
                series: [{
                    name: 'Expenses by Category',
                    type: 'pie',
                    radius: ['55%', '75%'],
                    avoidLabelOverlap: false,
                    itemStyle: {
                        borderRadius: 10,
                        borderColor: isDarkMode() ? '#292a33' : '#fff',
                        borderWidth: 2,
                    },

                    label: {
                        show: true,
                        position: 'outside',
                        formatter: '{b}',
                        textStyle: {
                            color: isDarkMode() ? '#fff' : '#000', // Dynamic text color based on theme
                            fontSize: 12, // Customize font size
                            fontWeight: 'bold', // Customize font weight
                            fontFamily: 'Arial', // Customize font family
                        }
                    },
                    emphasis: {
                        label: {
                            show: true,
                            fontSize: '22px',
                            fontWeight: 'normal'
                        }
                    },
                    labelLine: {
                        show: false
                    },
                    data: seriesData
                }]
            };

            // Initialize expense chart
            currentChart = echarts.init(document.querySelector("#chartContainer"));
            currentChart.setOption(options);
        }

        // Destroy the current chart
        function destroyChart1(chart) {
            if (chart) {
                chart.dispose();
            }
        }

        // Event listener for "Show Income" button
        document.getElementById('btn-income').addEventListener('click', function() {
            destroyChart1(expenseChart);
            initIncomeChart();
            document.getElementById('chart-title').textContent = '{{ __('Income By Category') }}';
            document.getElementById('btn-income').classList.add('btn-primary');
            document.getElementById('btn-expense').classList.remove('btn-primary');
        });

        // Event listener for "Show Expense" button
        document.getElementById('btn-expense').addEventListener('click', function() {
            destroyChart1(incomeChart);
            initExpenseChart();
            document.getElementById('chart-title').textContent = '{{ __('Expense By Category') }}';
            document.getElementById('btn-expense').classList.add('btn-primary');
            document.getElementById('btn-income').classList.remove('btn-primary');
        });

        // Initialize with the Income chart by default
        initIncomeChart();
    </script>

    <script>
        // Data from your weekly bill table
        var billTotal = {{ $weeklyBill['billTotal'] }};
        var billPaid = {{ $weeklyBill['billPaid'] }};
        var billDue = {{ $weeklyBill['billDue'] }};

        // Data from your monthly bill table
        var monthlyBillTotal = {{ $monthlyBill['billTotal'] }};
        var monthlyBillPaid = {{ $monthlyBill['billPaid'] }};
        var monthlyBillDue = {{ $monthlyBill['billDue'] }};

        // var currentChart2;
        // window.onresize = function() {
        //     currentChart2.resize();

        // };
        var previousZoom = window.devicePixelRatio;

        window.addEventListener('resize', function () {
            const currentZoom = window.devicePixelRatio;

            // Resize the chart in any case
            currentChart2.resize();

            // Additional handling if zoom level changed
            if (currentZoom !== previousZoom) {
                console.log("Zoom level changed!");
                previousZoom = currentZoom;
            }
        });

        // Pie chart options for the weekly bill
        var pieOptionWeekly = {
            tooltip: {
                trigger: 'item',
                formatter: '{b}: {c} ({d}%)'
            },
            legend: {
                top: '5%',
                left: '5%',
                orient: 'vertical',
                data: ['Total', 'Paid', 'Due'],
                textStyle: {
                    color: isDarkMode() ? '#fff' : '#000', // Dynamic text color based on theme
                    fontSize: 14, // Customize font size
                    fontWeight: 'bold', // Customize font weight
                    fontFamily: 'Arial', // Customize font family
                }
            },
            series: [{
                name: 'Weekly Bill',
                type: 'pie',
                radius: ['55%', '75%'], // Thinner ring
                avoidLabelOverlap: true,
                itemStyle: {
                    borderRadius: 10,
                    borderColor: isDarkMode() ? '#292a33' : '#fff',
                    borderWidth: 2
                },
                label: {
                    show: true,
                    position: 'outside',
                    formatter: '{b}: {c}',
                    textStyle: {
                        color: isDarkMode() ? '#fff' : '#000', // Dynamic text color based on theme
                        fontSize: 14, // Customize font size
                        fontWeight: 'bold', // Customize font weight
                        fontFamily: 'Arial', // Customize font family
                    }
                },
                emphasis: {
                    label: {
                        show: true,
                        fontSize: '22px',
                        fontWeight: 'normal'
                    }
                },
                data: [{
                        value: billTotal,
                        name: 'Total',
                        itemStyle: {
                            color: usedColor
                        }
                    },
                    {
                        value: billPaid,
                        name: 'Paid',
                        itemStyle: {
                            color: usedColorMedium
                        }
                    },
                    {
                        value: billDue,
                        name: 'Due',
                        itemStyle: {
                            color: usedColorDark
                        }
                    }
                ]
            }]
        };

        // Pie chart options for the monthly bill
        var pieOptionMonthly = {
            tooltip: {
                trigger: 'item',
                formatter: '{b}: {c} ({d}%)'
            },
            legend: {
                top: '5%',
                left: '5%',
                orient: 'vertical',
                data: ['Total', 'Paid', 'Due'],
                textStyle: {
                    color: isDarkMode() ? '#fff' : '#000', // Dynamic text color based on theme
                    fontSize: 14, // Customize font size
                    fontWeight: 'bold', // Customize font weight
                    fontFamily: 'Arial', // Customize font family
                }
            },
            series: [{
                name: 'Monthly Bill',
                type: 'pie',
                radius: ['55%', '75%'], // Thinner ring
                avoidLabelOverlap: true,
                itemStyle: {
                    borderRadius: 10,
                    borderColor: isDarkMode() ? '#292a33' : '#fff',

                    borderWidth: 2
                },
                label: {
                    show: true,
                    position: 'outside',
                    formatter: '{b}: {c}',
                    textStyle: {
                        color: isDarkMode() ? '#fff' : '#000', // Dynamic text color based on theme
                        fontSize: 14, // Customize font size
                        fontWeight: 'bold', // Customize font weight
                        fontFamily: 'Arial', // Customize font family
                    }
                },
                emphasis: {
                    label: {
                        show: true,
                        fontSize: '22px',
                        fontWeight: 'normal'
                    }
                },
                data: [{
                        value: monthlyBillTotal,
                        name: 'Total',
                        itemStyle: {
                            color: usedColorLight
                        }
                    },
                    {
                        value: monthlyBillPaid,
                        name: 'Paid',
                        itemStyle: {
                            color: usedColor
                        }
                    },
                    {
                        value: monthlyBillDue,
                        name: 'Due',
                        itemStyle: {
                            color: usedColorDarker
                        }
                    }
                ]
            }]
        };
        // Destroy the current chart
        function destroyChart2() {
            if (currentChart2) {
                currentChart2.dispose();
            }
        }
        // Function to initialize the weekly bill chart
        function initWeeklyChart() {
            if (!currentChart2) {
                currentChart2 = echarts.init(document.getElementById('billChartContainer'));
            }
            currentChart2.setOption(pieOptionWeekly);
        }

        // Function to initialize the monthly bill chart
        function initMonthlyChart() {
            if (!currentChart2) {
                currentChart2 = echarts.init(document.getElementById('billChartContainer'));
            }
            currentChart2.setOption(pieOptionMonthly);
        }

        // Function to destroy the current chart
        function destroyChart2() {
            if (currentChart2) {
                currentChart2.dispose(); // Dispose the chart
                currentChart2 = null; // Reset the chart variable
            }
        }

        // Event listener for "Weekly Statistics" button
        document.getElementById('btn-weekly').addEventListener('click', function() {
            destroyChart2();
            initWeeklyChart();
            document.getElementById('chart-title').textContent = '{{ __('Bills Weekly Statistics') }}';
            document.getElementById('btn-weekly').classList.add('btn-primary');
            document.getElementById('btn-monthly').classList.remove('btn-primary');
        });

        // Event listener for "Monthly Statistics" button
        document.getElementById('btn-monthly').addEventListener('click', function() {
            destroyChart2();
            initMonthlyChart();
            document.getElementById('chart-title').textContent = '{{ __('Bills Monthly Statistics') }}';
            document.getElementById('btn-monthly').classList.add('btn-primary');
            document.getElementById('btn-weekly').classList.remove('btn-primary');
        });

        // Initialize the Weekly chart by default (since it’s active by default)
        initWeeklyChart();
    </script>
    <script></script>
@endpush
{{-- @section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Account') }}</li>
@endsection --}}
@section('content')
    {{-- <style>

    /* path{
        stroke-linecap: round !important;
    } */
</style> --}}

    {{-- <style>
        body {
            font-family: Arial, sans-serif;
        }

        .chart-container {
            width: auto !important;
            height: 400px;
            position: relative;
            margin: 0 auto;
        }

        svg {
            width: 100%;
            height: 100%;
        }

        .tooltip {
            position: absolute;
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 5px;
            border-radius: 5px;
            font-size: 12px;
            display: none;
        }

        .line {
            fill: none;
            stroke-width: 3;
        }

        .income-line {
            stroke: #36a2eb;
            /* Light blue */
        }

        .expense-line {
            stroke: #4b4bc0;
            /* Dark blue */
        }

       
    </style> --}}
    <style>
        #limit-chart213,
        #limit-chart123 {
            height: 500px !important;
            width: 100%;
        }

        #incomeByCategory div canvas,
        #expenseByCategory div canvas,
        #yearlyChart div canvas,
        #line_chart_123 div canvas,
        #limit-chart213 div canvas,
        #limit-chart123 div canvas,
        #main123456789 div canvas {
            width: 100% !important;
        }

        #line_chart_123 {
            height: 500px !important;
            width: 100%;
        }

        .image-icon {
            height: auto !important;
            width: 25px !important;
        }
    </style>
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-lg-3 col-12 col-sm-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-flex-row">
                                                <div class="theme-avtar bg-primary">
                                                    <img class="image-icon" src="{{ asset('/assets/images/1.png') }}"
                                                        alt="">
                                                </div>
                                                {{-- <p class="text-muted text-sm mt-4 mb-2">{{__('Total')}}</p> --}}
                                                <div class="col-12">
                                                    <h6 class="mb-0">{{ __('Total Customers') }}</h6>
                                                    <h3 class="mb-0">{{ \Auth::user()->countCustomers() }}

                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-12 col-sm-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-flex-row">
                                                <div class="theme-avtar bg-primary">
                                                    <img class="image-icon" src="{{ asset('/assets/images/2.png') }}"
                                                        alt="">
                                                    {{-- <i class="ti ti-users"></i> --}}
                                                </div>
                                                <div class="col-12">
                                                    <h6 class="mb-0">{{ __('Total Vendors') }}</h6>
                                                    <h3 class="mb-0">{{ \Auth::user()->countVenders() }}</h3>
                                                </div>
                                            </div>
                                            {{-- <p class="text-muted text-sm mt-4 mb-2">{{__('Total')}}</p> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-12 col-sm-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-flex-row">
                                                <div class="theme-avtar bg-primary">
                                                    <img class="image-icon" src="{{ asset('/assets/images/3.png') }}"
                                                        alt="">
                                                    {{-- <i class="ti ti-report-money"></i> --}}
                                                </div>
                                                {{-- <p class="text-muted text-sm mt-4 mb-2">{{__('Total')}}</p> --}}
                                                <div class="col-12">
                                                    <h6 class="mb-0">{{ __('Total Invoices') }}</h6>
                                                    <h3 class="mb-0">{{ \Auth::user()->countInvoices() }} </h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-12 col-sm-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-flex-row">
                                                <div class="theme-avtar bg-primary">
                                                    <img class="image-icon" src="{{ asset('/assets/images/4.png') }}"
                                                        alt="">
                                                    {{-- <i class="ti ti-report-money"></i> --}}
                                                </div>
                                                {{-- <p class="text-muted text-sm mt-4 mb-2">{{__('Total')}}</p> --}}
                                                <div class="col-12">
                                                    <h6 class="mb-0">{{ __('Total Bills') }}</h6>
                                                    <h3 class="mb-0">{{ \Auth::user()->countBills() }} </h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-xxl-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Income & Expense') }}
                                        <span
                                            class="float-end text-muted">{{ __('Current Year') . ' - ' . $currentYear }}</span>
                                    </h5>

                                </div>
                                <style>
                                    .chart-container {
                                        position: relative;
                                        margin: auto;
                                        height: 500px;
                                        /* Increased height */
                                        width: 100%;
                                        /* Ensures the width fits the parent */
                                    }

                                    .margin_inline {
                                        margin-inline: 1rem;
                                    }

                                    .height-50 {
                                        height: 50vh;
                                    }

                                    .height-40 {
                                        height: 60vh;
                                    }
                                </style>
                                <div class="card-body chart-container">
                                    {{-- <div id="incExpBarChart"></div> --}}
                                    <div id="main123456789"style="height: 100%; width: 100%;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Storage Limit') }}
                                        {{--                                        <span class="float-end text-muted">{{__('Year').' - '.$currentYear}}</span> --}}
                                        <small class="float-end text-muted">{{ $users->storage_limit . 'MB' }} /
                                            {{ $plan->storage_limit . 'MB' }}</small>
                                    </h5>
                                </div>
                                <style>
                                    .height-responsive-40 {
                                        height: 60vh;
                                    }

                                    @media(max-width:991px) {
                                        .card-body.height-40 {
                                            height: 1100px;
                                        }

                                        .height-responsive-40 {
                                            height: auto !important;
                                            ;
                                        }
                                    }
                                </style>
                                <div class="card-body height-40">
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 ">
                                            <div id="limit-chart213" class="height-40"></div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div id="limit-chart123" class="height-40"></div>
                                        </div>
                                    </div>
                                    <div class="graph-content mt-3">
                                        <p>Some content goes here </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mt-1 mb-0">{{ __('Income Vs Expense') }}</h5>
                                </div>
                                <div class="card-body height-responsive-40">
                                    <div class="row">
                                        <div class="col-sm-12 my-2 border-b">
                                            <div
                                                class="d-flex align-items-start justify-content-between mb-2 margin_inline">
                                                <div class="d-flex">
                                                    <div class="theme-avtar bg-primary">
                                                        <img class="image-icon" src="{{ asset('/assets/images/2.1.png') }}"
                                                            alt="">
                                                        {{-- <i class="ti ti-report-money"></i> --}}
                                                    </div>
                                                    <div class="ms-2">
                                                        <p class="text-primary text-sm mb-0">{{ __('Income Today') }}</p>
                                                        <p class="text-primary text-sm mb-0">{{ __('Total Income') }}</p>
                                                    </div>
                                                </div>
                                                <h4 class="mb-0 ">
                                                    {{ \Auth::user()->priceFormat(\Auth::user()->todayIncome()) }}</h4>

                                            </div>
                                        </div>

                                        <div class="col-sm-12 my-2 border-b">
                                            <div
                                                class="d-flex align-items-start justify-content-between mb-2 margin_inline">
                                                <div class="d-flex">
                                                    <div class="theme-avtar bg-primary">
                                                        <img class="image-icon" src="{{ asset('/assets/images/2.2.png') }}"
                                                            alt="">
                                                        {{-- <i class="ti ti-file-invoice"></i> --}}
                                                    </div>
                                                    <div class="ms-2">
                                                        <p class="text-primary text-sm mb-0">{{ __('Expense Today') }}</p>
                                                        <p class="text-primary text-sm mb-0">{{ __('Total Expense') }}</p>
                                                    </div>
                                                </div>
                                                <h4 class="mb-0 ">
                                                    {{ \Auth::user()->priceFormat(\Auth::user()->todayExpense()) }}
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 my-2 border-b">

                                            <div
                                                class="d-flex align-items-start justify-content-between mb-2 margin_inline">
                                                <div class="d-flex">
                                                    <div class="theme-avtar bg-primary">
                                                        <img class="image-icon"
                                                            src="{{ asset('/assets/images/2.3.png') }}" alt="">
                                                        {{-- <i class="ti ti-report-money"></i> --}}
                                                    </div>
                                                    <div class="ms-2">
                                                        <p class="text-primary text-sm mb-0">{{ __('Income This Month') }}
                                                        </p>
                                                        <p class="text-primary text-sm mb-0">{{ __('Total Income') }}</p>
                                                    </div>
                                                </div>
                                                <h4 class="mb-0 ">
                                                    {{ \Auth::user()->priceFormat(\Auth::user()->incomeCurrentMonth()) }}
                                                </h4>
                                            </div>

                                        </div>
                                        <div class=" col-sm-12 my-2 border-b">
                                            <div
                                                class="d-flex align-items-start justify-content-between mb-2 margin_inline">
                                                <div class="d-flex">
                                                    <div class="theme-avtar bg-primary">
                                                        <img class="image-icon"
                                                            src="{{ asset('/assets/images/2.4.png') }}" alt="">
                                                        {{-- <i class="ti ti-file-invoice"></i> --}}
                                                    </div>
                                                    <div class="ms-2">
                                                        <p class="text-primary text-sm mb-0">
                                                            {{ __('Expense This Month') }}
                                                        </p>
                                                        <p class="text-primary text-sm mb-0">{{ __('Total Expense') }}</p>
                                                    </div>
                                                </div>
                                                <h4 class="mb-0 ">
                                                    {{ \Auth::user()->priceFormat(\Auth::user()->expenseCurrentMonth()) }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mt-1 mb-0">{{ __('Cashflow') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div id="line_chart_123"></div>
                                    {{-- <div id="cash-flow"></div> --}}
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="btn-graph-bar">
                                        <div class="rounded text-white p-2  btn-graph-toggler" id="showWeekly"
                                            onclick="showChart('weeklyChart','showWeekly')">Week</div>
                                        <div class="rounded text-white p-2  btn-graph-toggler" id="showMonthly"
                                            onclick="showChart('monthlyChart','showMonthly')">Month</div>
                                        <div class="rounded text-white p-2  btn-graph-toggler" id="showYearly"
                                            onclick="showChart('yearlyChart','showYearly')">Year</div>
                                    </div>
                                </div>
                                <div class="card-body">


                                    <style>
                                        .btn-graph-bar {
                                            background: #f8f7fc;
                                            display: flex;
                                            justify-content: center;
                                            align-items: center;
                                            gap: 1rem;
                                            padding-block: 0.3rem;
                                            padding-inline: 2rem;
                                            width: max-content;
                                            margin-inline: auto;
                                            border: 1px solid #f8f7fc;
                                            border-radius: 20px;
                                        }

                                        .btn-graph-toggler {
                                            background: transparent;
                                            color: black !important;
                                            transition: all 0.03s ease-out;
                                            cursor: pointer;
                                        }

                                        .btn-graph-toggler.active {
                                            background: #ffff;
                                            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
                                            padding: 0.6rem !important;
                                        }

                                        .charts-area {
                                            height: auto !important;

                                        }

                                        #incomeByCategory,
                                        #expenseByCategory {
                                            height: 30vh !important;
                                            width: 100%;
                                        }


                                        /* Default styling for the charts */
                                        .chart {
                                            visibility: hidden;
                                            position: absolute;
                                            width: 100% !important;
                                            height: 40vh !important;
                                        }

                                        /* Show weekly chart by default */
                                        #yearlyChart {
                                            visibility: visible;
                                            position: relative;
                                        }
                                    </style>
                                    <div class="charts-area">
                                        {{-- <div class="table-responsive"> --}}
                                        <div id="weeklyChart" class="chart"></div>
                                        <div id="monthlyChart" class="chart"></div>
                                        <div id="yearlyChart" class="chart"></div>
                                        {{-- <div id="pie_chart_111" style="height: 30vh; width: 50vh;"></div> --}}
                                        {{-- </div> --}}
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <ul class="nav nav-pills mb-5" id="pills-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                                                href="#invoice_weekly_statistics" role="tab"
                                                aria-controls="pills-home"
                                                aria-selected="true">{{ __('Invoices Weekly Statistics') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-profile-tab" data-bs-toggle="pill"
                                                href="#invoice_monthly_statistics" role="tab"
                                                aria-controls="pills-profile"
                                                aria-selected="false">{{ __('Invoices Monthly Statistics') }}</a>
                                        </li>
                                    </ul> --}}
                    {{-- <div class="row">
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Income By Category') }}
                                        <span class="float-end text-muted">{{ __('Year') . ' - ' . $currentYear }}</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="incomeByCategory"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Expense By Category') }}
                                        <span class="float-end text-muted">{{ __('Year') . ' - ' . $currentYear }}</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="expenseByCategory"></div>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <div class="row">
                        <div class=" col-xl-12 col-xxl-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <h5 id="chart-title">{{ __('Income By Category') }}</h5>
                                    <div>
                                        <button id="btn-income"
                                            class="btn btn-primary">{{ __('Income') }}</button>
                                        <button id="btn-expense"
                                            class="btn ">{{ __('Expense') }}</button>
                                    </div>
                                </div>
                                <div class="card-body height-50">
                                    <div id="chartContainer" style="width: 100%; height: 400px;"></div>
                                </div>
                            </div>
                        </div>


                        <div class="col-xl-12 col-xxl-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <h5 id="chart-title">{{ __('Bills Weekly Statistics') }}</h5>
                                    <div>
                                        <button id="btn-weekly"
                                            class="btn btn-primary">{{ __('Weekly Statistics') }}</button>
                                        <button id="btn-monthly" class="btn ">{{ __('Monthly Statistics') }}</button>
                                    </div>
                                </div>
                                <div class="card-body height-50">
                                    <div class="table-responsive">
                                        <div id="billChartContainer" style="width: 100%; height: 400px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                </div>
            </div>
            {{-- <table class="table align-items-center mb-0 ">
                                                <tbody class="list">
                                                    <tr>
                                                        <td>
                                                            <h5 class="mb-0">{{ __('Total') }}</h5>
                                                            <p class="text-muted text-sm mb-0">
                                                                {{ __('Bill Generated') }}</p>

                                                        </td>
                                                        <td>
                                                            <h4 class="text-muted">
                                                                {{ \Auth::user()->priceFormat($weeklyBill['billTotal']) }}
                                                            </h4>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <h5 class="mb-0">{{ __('Total') }}</h5>
                                                            <p class="text-muted text-sm mb-0">{{ __('Paid') }}
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <h4 class="text-muted">
                                                                {{ \Auth::user()->priceFormat($weeklyBill['billPaid']) }}
                                                            </h4>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <h5 class="mb-0">{{ __('Total') }}</h5>
                                                            <p class="text-muted text-sm mb-0">{{ __('Due') }}
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <h4 class="text-muted">
                                                                {{ \Auth::user()->priceFormat($weeklyBill['billDue']) }}
                                                            </h4>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table> --}}{{-- <table class="table align-items-center mb-0 ">
                                                <tbody class="list">
                                                    <tr>
                                                        <td>
                                                            <h5 class="mb-0">{{ __('Total') }}</h5>
                                                            <p class="text-muted text-sm mb-0">
                                                                {{ __('Bill Generated') }}</p>

                                                        </td>
                                                        <td>
                                                            <h4 class="text-muted">
                                                                {{ \Auth::user()->priceFormat($monthlyBill['billTotal']) }}
                                                            </h4>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <h5 class="mb-0">{{ __('Total') }}</h5>
                                                            <p class="text-muted text-sm mb-0">{{ __('Paid') }}
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <h4 class="text-muted">
                                                                {{ \Auth::user()->priceFormat($monthlyBill['billPaid']) }}
                                                            </h4>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <h5 class="mb-0">{{ __('Total') }}</h5>
                                                            <p class="text-muted text-sm mb-0">{{ __('Due') }}
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <h4 class="text-muted">
                                                                {{ \Auth::user()->priceFormat($monthlyBill['billDue']) }}
                                                            </h4>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table> --}}
            <style>
                .card-body {
                    padding: 20px;
                    /* Add some padding to the card */
                }

                .table {
                    width: 100%;
                    /* Ensure the table takes full width */
                    border-collapse: collapse;
                    /* Remove gaps between cells */
                }

                .table thead {
                    background-color: var(--used-color) !important;
                    /* Light blue background for the header */
                    padding: 10px;
                    /* Padding inside header cells */
                    text-align: left;
                    /* Left-align text */
                    border-bottom: 2px solid #dee2e6;
                    /* Bottom border for header */
                    border-radius: 25px;
                    width: 80% !important;
                }

                .table thead th {
                    color: white !important;
                }
            </style>

            {{-- <div class="col-xxl-12">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mt-1 mb-0">{{ __('Account Balance') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr id="thead123">
                                                <th>{{ __('Bank') }}</th>
                                                <th>{{ __('Holder Name') }}</th>
                                                <th>{{ __('Balance') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($bankAccountDetail as $bankAccount)
                                                <tr class="font-style">
                                                    <td>{{ $bankAccount->bank_name }}</td>
                                                    <td>{{ $bankAccount->holder_name }}</td>
                                                    <td>{{ \Auth::user()->priceFormat($bankAccount->opening_balance) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4">
                                                        <div class="text-center">
                                                            <h6>{{ __('there is no account balance') }}</h6>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mt-1 mb-0">{{ __('Latest Income') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Customer') }}</th>
                                                <th>{{ __('Amount Due') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($latestIncome as $income)
                                                <tr>
                                                    <td>{{ \Auth::user()->dateFormat($income->date) }}</td>
                                                    <td>{{ !empty($income->customer) ? $income->customer->name : '-' }}
                                                    </td>
                                                    <td>{{ \Auth::user()->priceFormat($income->amount) }}</td>
                                                </tr>
                                            @empty
                                                <tr>

                                                    <td colspan="4">
                                                        <div class="text-center">
                                                            <h6>{{ __('There is no latest income') }}</h6>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mt-1 mb-0">{{ __('Latest Expense') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Vendor') }}</th>
                                                <th>{{ __('Amount Due') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($latestExpense as $expense)
                                                <tr>
                                                    <td>{{ \Auth::user()->dateFormat($expense->date) }}</td>
                                                    <td>{{ !empty($expense->vender) ? $expense->vender->name : '-' }}
                                                    </td>
                                                    <td>{{ \Auth::user()->priceFormat($expense->amount) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4">
                                                        <div class="text-center">
                                                            <h6>{{ __('There is no latest expense') }}</h6>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-xxl-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mt-1 mb-0">{{ __('Recent Invoices') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('Customer') }}</th>
                                                <th>{{ __('Issue Date') }}</th>
                                                <th>{{ __('Due Date') }}</th>
                                                <th>{{ __('Amount') }}</th>
                                                <th>{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentInvoice as $invoice)
                                                <tr>
                                                    <td>{{ \Auth::user()->invoiceNumberFormat($invoice->invoice_id) }}
                                                    </td>
                                                    <td>{{ !empty($invoice->customer) ? $invoice->customer->name : '' }}
                                                    </td>
                                                    <td>{{ Auth::user()->dateFormat($invoice->issue_date) }}</td>
                                                    <td>{{ Auth::user()->dateFormat($invoice->due_date) }}</td>
                                                    <td>{{ \Auth::user()->priceFormat($invoice->getTotal()) }}</td>
                                                    <td>
                                                        @if ($invoice->status == 0)
                                                            <span
                                                                class="p-2 px-3 rounded badge status_badge bg-secondary">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                                        @elseif($invoice->status == 1)
                                                            <span
                                                                class="p-2 px-3 rounded badge status_badge bg-warning">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                                        @elseif($invoice->status == 2)
                                                            <span
                                                                class="p-2 px-3 rounded badge status_badge bg-danger">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                                        @elseif($invoice->status == 3)
                                                            <span
                                                                class="p-2 px-3 rounded badge status_badge bg-info">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                                        @elseif($invoice->status == 4)
                                                            <span
                                                                class="p-2 px-3 rounded badge status_badge bg-primary">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6">
                                                        <div class="text-center">
                                                            <h6>{{ __('There is no recent invoice') }}</h6>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mt-1 mb-0">{{ __('Recent Bills') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('Vendor') }}</th>
                                                <th>{{ __('Bill Date') }}</th>
                                                <th>{{ __('Due Date') }}</th>
                                                <th>{{ __('Amount') }}</th>
                                                <th>{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentBill as $bill)
                                                <tr>
                                                    <td>{{ \Auth::user()->billNumberFormat($bill->bill_id) }}</td>
                                                    <td>{{ !empty($bill->vender) ? $bill->vender->name : '' }} </td>
                                                    <td>{{ Auth::user()->dateFormat($bill->bill_date) }}</td>
                                                    <td>{{ Auth::user()->dateFormat($bill->due_date) }}</td>
                                                    <td>{{ \Auth::user()->priceFormat($bill->getTotal()) }}</td>
                                                    <td>
                                                        @if ($bill->status == 0)
                                                            <span
                                                                class="p-2 px-3 rounded badge bg-secondary">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                                        @elseif($bill->status == 1)
                                                            <span
                                                                class="p-2 px-3 rounded badge bg-warning">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                                        @elseif($bill->status == 2)
                                                            <span
                                                                class="p-2 px-3 rounded badge bg-danger">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                                        @elseif($bill->status == 3)
                                                            <span
                                                                class="p-2 px-3 rounded badge bg-info">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                                        @elseif($bill->status == 4)
                                                            <span
                                                                class="p-2 px-3 rounded badge bg-primary">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6">
                                                        <div class="text-center">
                                                            <h6>{{ __('There is no recent bill') }}</h6>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div> --}}
            {{-- <div class="col-xxl-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Goal') }}</h5>
                    </div>
                    <div class="card-body">
                        @forelse($goals as $goal)
                            @php
                                $total = $goal->target($goal->type, $goal->from, $goal->to, $goal->amount)['total'];
                                $percentage = $goal->target($goal->type, $goal->from, $goal->to, $goal->amount)[
                                    'percentage'
                                ];
                                $per = number_format(
                                    $goal->target($goal->type, $goal->from, $goal->to, $goal->amount)['percentage'],
                                    Utility::getValByName('decimal_number'),
                                    '.',
                                    '',
                                );
                            @endphp
                            <div class="card border-success border-2 border-bottom-0 border-start-0 border-end-0">
                                <div class="card-body">
                                    <div class="form-check">
                                        <label class="form-check-label d-block" for="customCheckdef1">
                                            <span>
                                                <span class="row align-items-center">
                                                    <span class="col">
                                                        <span class="text-muted text-sm">{{ __('Name') }}</span>
                                                        <h6 class="text-nowrap mb-3 mb-sm-0">{{ $goal->name }}</h6>
                                                    </span>
                                                    <span class="col">
                                                        <span class="text-muted text-sm">{{ __('Type') }}</span>
                                                        <h6 class="mb-3 mb-sm-0">
                                                            {{ __(\App\Models\Goal::$goalType[$goal->type]) }}</h6>
                                                    </span>
                                                    <span class="col">
                                                        <span class="text-muted text-sm">{{ __('Duration') }}</span>
                                                        <h6 class="mb-3 mb-sm-0">
                                                            {{ $goal->from . ' To ' . $goal->to }}
                                                        </h6>
                                                    </span>
                                                    <span class="col">
                                                        <span class="text-muted text-sm">{{ __('Target') }}</span>
                                                        <h6 class="mb-3 mb-sm-0">
                                                            {{ \Auth::user()->priceFormat($total) . ' of ' . \Auth::user()->priceFormat($goal->amount) }}
                                                        </h6>
                                                    </span>
                                                    <span class="col">
                                                        <span class="text-muted text-sm">{{ __('Progress') }}</span>
                                                        <h6 class="mb-2 d-block">
                                                            {{ number_format($goal->target($goal->type, $goal->from, $goal->to, $goal->amount)['percentage'], Utility::getValByName('decimal_number'), '.', '') }}%
                                                        </h6>
                                                        <div class="progress mb-0">
                                                            @if ($per <= 33)
                                                                <div class="progress-bar bg-danger"
                                                                    style="width: {{ $per }}%"></div>
                                                            @elseif($per >= 33 && $per <= 66)
                                                                <div class="progress-bar bg-warning"
                                                                    style="width: {{ $per }}%"></div>
                                                            @else
                                                                <div class="progress-bar bg-primary"
                                                                    style="width: {{ $per }}%"></div>
                                                            @endif
                                                        </div>
                                                    </span>
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="card pb-0">
                                <div class="card-body text-center">
                                    <h6>{{ __('There is no goal.') }}</h6>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
    </div>
@endsection
