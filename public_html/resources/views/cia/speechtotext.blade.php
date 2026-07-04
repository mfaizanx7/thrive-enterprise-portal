@extends('layouts.admin')

@section('page-title')
    {{ __('Dynamic Reporting') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Workflow') }}</li>
@endsection
@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        // const usedColor = getComputedStyle(document.body).getPropertyValue('--used-color').trim();
        // const usedColorLight = getComputedStyle(document.body).getPropertyValue('--used-color-light').trim();
        // const usedColorMedium = getComputedStyle(document.body).getPropertyValue('--used-color-medium').trim();
        // const usedColorDark = getComputedStyle(document.body).getPropertyValue('--used-color-dark').trim();
        // const usedColorDarker = getComputedStyle(document.body).getPropertyValue('--used-color-darker').trim();
        // const usedColorContrast = getComputedStyle(document.body).getPropertyValue('--used-color-contrast').trim();
    </script>

    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <style>
            .container {
                max-width: 100vw;
                margin: 0 auto;
                background: #fff;
                border-radius: 10px;
                padding: 20px;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            }

            h3 {
                font-size: 1.5rem;
                color: #555;
                text-align: center;
            }

            #promptInput {
                width: 100%;
                height: 80px;
                border-radius: 8px;
                padding: 10px;
                font-size: 1rem;
                margin-bottom: 15px;
                border:#fff;
            }

            .btn {
                display: inline-block;
                color: #fff;
                padding: 10px 20px;
                border: none;
                border-radius: 8px;
                font-size: 1rem;
                font-weight: bold;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn:hover {
                transform: translateY(-5px);
            }

            #resultsTable {
                margin-top: 30px;
                text-align: center;
            }

            #output {
                margin-top: 20px;
                font-size: 1.2rem;
                font-weight: bold;
                color: #333;
                text-align: center;
            }

            .icon-box {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 20px;
                margin: 30px 0;
            }

            .icon-box i {
                font-size: 2rem;
                color:#fff;
            }

            .icon-box span {
                font-size: 1rem;
                font-weight: bold;
                color: #444;
            }

            .icon-box::before {
                display: none;
            }

            .custom-table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                font-size: 16px;
                text-align: left;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .custom-table th,
            .custom-table td {
                border: 1px solid #ddd;
                padding: 10px;
            }

            .custom-table th {
                color: #fff;
            }

            .custom-table tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .custom-table tr:hover {
                background-color: #f1f1f1;
            }

            .d-none {
                display: none !important;
            }
        </style>
    </head>
    <div class="container">
        <input type="text" id="promptInput" placeholder="Enter your prompt here..." />
        <br>
        <button class="btn" id="submitPromptButton"><i class="fas fa-paper-plane"></i> {{ __('Submit Prompt') }}</button>
        <div id="output"></div>

        <div class="icon-box">
            <i class="fas fa-chart-bar"></i>
            <i class="fas fa-database"></i>
            <i class="fas fa-cogs"></i>
            <span>{{ __('Generate, Analyze, and Optimize your reports dynamically!') }}</span>
        </div>
        <div style="text-align: center; font-size:2rem; font-weight:800;"> Result </div>
        <button class="btn d-none" id="exportButton" style="float: right;"><i class="fas fa-file-excel"></i>
            {{ __('Export to Excel') }}</button><br>
        <div id="resultsTable" class="table-responsive"></div>

        <h3><i class="fas fa-exclamation-circle"></i>
            {{ __('This Assistant may make mistakes. Please verify important information.') }}</h3>

    </div>
    <script>
        document.getElementById('exportButton').addEventListener('click', function() {
            const table = document.querySelector('.custom-table');
            const workbook = XLSX.utils.table_to_book(table, {
                sheet: "Sheet1"
            });
            XLSX.writeFile(workbook, 'Dynamic_Report.xlsx');
        });
    </script>

    <script type="importmap">
        {
          "imports": {
            "@google/generative-ai": "https://esm.run/@google/generative-ai"
          }
        }
      </script>
    <script type="module">
        import {
            GoogleGenerativeAI
        } from "@google/generative-ai";
        document.querySelector('#promptInput').style.border = `2px solid ${usedColor}`;
        document.querySelectorAll('.icon-box i').forEach(function(icon) {
            icon.style.color = usedColor;
            
        });
        const startButton = document.getElementById('startButton');
        const outputDiv = document.getElementById('output');
        const promptInput = document.getElementById('promptInput');
        const submitPromptButton = document.getElementById('submitPromptButton');
        submitPromptButton.style.background = `linear-gradient(135deg, ${usedColorDarker}, ${usedColor})`;
        submitPromptButton.style.color = '#fff';
        const exportExcelbtn = document.getElementById('exportButton');
        exportExcelbtn.style.background = `linear-gradient(135deg, ${usedColorDarker}, ${usedColor})`;
        submitPromptButton.addEventListener('click', () => {
            const prompt = promptInput.value;
            if (prompt != '') {
                executeSQLQuery(prompt);
            } else {
                alert('Please enter a prompt.');
            }
        });
        const recognition = new(window.SpeechRecognition || window.webkitSpeechRecognition ||
            window.mozSpeechRecognition || window.msSpeechRecognition)();
        let isListeningForCommand = false;
        recognition.continuous = true;
        recognition.interimResults = true;
        let silenceTimeout;

        recognition.onstart = () => {
            startButton.textContent = 'Listening...';
            resetSilenceTimeout();
        };

        recognition.onresult = (event) => {
            let transcript = Array.from(event.results)
                .map(result => result[0].transcript)
                .join('');

            outputDiv.textContent = `${transcript}`;
            isListeningForCommand = true;
            resetSilenceTimeout(transcript);
        };

        recognition.onend = () => {
            startButton.textContent = 'Start Voice Input';
            clearTimeout(silenceTimeout);
        };

        if(startButton) {
            startButton.addEventListener('click', () => {
                recognition.start();
            });
        }

        function resetSilenceTimeout(transcript) {
            clearTimeout(silenceTimeout);
            silenceTimeout = setTimeout(() => {
                recognition.stop();
                if (isListeningForCommand) {
                    executeSQLQuery(transcript);
                }
                isListeningForCommand = false;
            }, 5000);
        }

        function executeSQLQuery(query) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const dynamicreportingurl = '{{ route('dynamic_reporting') }}';
            const submitButton = document.getElementById('submitPromptButton');
            submitButton.disabled = true;
            submitButton.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Processing, please wait...`;
            fetch(dynamicreportingurl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        prompt: query
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Query Execution Result:', data);
                    const tableContainer = document.getElementById('resultsTable');
                    tableContainer.innerHTML = '';
                    if (data.success && data.results.length > 0) {
                        data.results.forEach(result => {
                            if (result.data && result.data.length > 0) {
                                const table = document.createElement('table');
                                table.classList.add('custom-table');

                                const headerRow = document.createElement('tr');
                                const headers = Object.keys(result.data[0]);

                                headers.forEach(header => {
                                    const th = document.createElement('th');
                                    th.innerText = formatHeader(header);
                                    headerRow.appendChild(th);
                                });
                                table.appendChild(headerRow);
                                result.data.forEach(row => {
                                    const tableRow = document.createElement('tr');
                                    headers.forEach(header => {
                                        const td = document.createElement('td');
                                        td.innerText = row[header] ||
                                            '-';
                                        tableRow.appendChild(td);
                                    });
                                    table.appendChild(tableRow);
                                });
                                tableContainer.appendChild(table);
                                exportExcelbtn.classList.remove('d-none');
                                document.querySelectorAll('.custom-table th').forEach(function(th) {
                                    th.style.backgroundColor = usedColor; 
                                });
                                submitButton.disabled = false;
                                submitButton.innerHTML =
                                    `<i class="fas fa-paper-plane"></i> {{ __('Submit Prompt') }}`;
                            } else {
                                tableContainer.innerHTML = '<p>No data found.</p>';
                                submitButton.disabled = false;
                                submitButton.innerHTML =
                                    `<i class="fas fa-paper-plane"></i> {{ __('Submit Prompt') }}`;
                            }
                        });
                    } else {
                        tableContainer.innerHTML = '<p>No results to display.</p>';
                        submitButton.disabled = false;
                        submitButton.innerHTML = `<i class="fas fa-paper-plane"></i> {{ __('Submit Prompt') }}`;
                    }
                })
                .catch(error => {
                    console.error('Error executing SQL query:', error);
                    submitButton.disabled = false;
                    submitButton.innerHTML = `<i class="fas fa-paper-plane"></i> {{ __('Submit Prompt') }}`;
                });
        }

        function formatHeader(header) {
            return header
                .replace(/_/g, ' ')
                .toLowerCase()
                .replace(/\b\w/g, char => char.toUpperCase());
        }

        function readOut(message) {
            const speech = new SpeechSynthesisUtterance();
            const allVoices = speechSynthesis.getVoices();
            speech.text = message;
            speech.voice = allVoices[36];
            speech.volume = 1;
            window.speechSynthesis.speak(speech);
        }
    </script>
@endsection