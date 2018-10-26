class Analyzer {

    getTrafficName(item) {
        let trafficName;
        if (item.target.classList.contains('traffic')) {
            return trafficName = item.target.dataset.table;
        }
    }

    deleteTrafficTable(table) {
        fetch('/ajax/deleteTrafficTable', {
            method: 'post',
            headers: {
                "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
            },
            body: 'trafficTableName=' + table
        }).then(function (res) {

        })
            .then(function (data) {

            })
            .catch(function (error) {
                console.log('Request failed', error);
            });
    }

    analyzeTraffic(table) {
        fetch('/ajax/analyze', {
            method: 'post',
            headers: {
                "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
            },
            body: 'analyze=' + table
        })
            .then(function (res) {
                return res.json();
            })
            .then(function (data) {
                console.log(data.length);
                let tbody = document.getElementById('statistic_table');
                let table = document.getElementById('stat_table_container');
                let info = document.getElementById('stat_info');
                info.classList.add('hide');
                table.classList.remove('hide');
                tbody.innerHTML = '';
                for (const key of Object.keys(data)) {
                    let tr = document.createElement('tr');
                    tbody.append(tr);
                    for (let item in data[key]) {
                        let td = document.createElement('td');
                        td.innerText = data[key][item];
                        tr.append(td);
                    }
                }
            })
            .catch(function (error) {
                console.log('Request failed', error);
            });
    }

    reactTable() {
        var input, filter, table, tr, td, i;
        input = document.getElementById("tb-search");
        filter = input.value.toUpperCase();
        table = document.getElementById("stat_table");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

    launchMap(data) {
        if(this.map) {
            this.map.remove();
        }
        console.table(data);
        let map = L.map('sb_map', {preferCanvas: true}).setView([48.35, 39.20], 10);

        let TopoLayer = L.tileLayer('/public/map-tiles/{z}/{x}/{y}.png', {
            maxZoom: 9,
            minZoom: 5,
        });

        map.addLayer(TopoLayer);

        for (let i = 0; i < data.length; i++) {

            let lat = data[i].lat;
            let lon = data[i].lon;
            let range = data[i].range;
            if (lat !== null) {

                let customPopup = `<p><strong>тел. ТА:</strong>${data[i].ta}</p><p><strong>азімут/адреса: </strong>${data[i].position}</p>`;

                let customOptions =
                    {
                        'maxWidth': '150',
                        'className': 'custom'
                    };

                let circleMarker = L.circleMarker([lat, lon], {
                    color: '#c82333',
                    radius: 5,
                    zIndexOffset: 5,
                    bubblingMouseEvents: true
                }).bindPopup(customPopup, customOptions).addTo(map);
            }
            console.log(map);

        }

        document.getElementById('sb_map').style.display = 'block';
        setTimeout(function () {
            map.invalidateSize()
        }, 800);


    }

    getAllConnections(tableName, ressolve) {
        fetch('/ajax/getallconnections', {
            method: 'post',
            headers: {
                "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
            },
            body: 'getallconnections=' + tableName
        }).then(function (res) {
            return res.json();
        }).then(function (data) {
            ressolve(data);
        });
    }

    getresultFromTraffic(table,analyzeBtn) {
        fetch('/ajax/getResultTraffic', {
            method: 'post',
            headers: {
                "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
            },
            body: 'getResultTraffic=' + table
        }).then(function (res) {
            analyzeBtn.childNodes[1].classList.remove('fa-spin');
            analyzeBtn.childNodes[1].classList.remove('fa-spinner');
            analyzeBtn.childNodes[1].classList.add('fa-check');
            return res.json();
        })
            .then(function (data) {
                let resultContainer = document.getElementById('resultContainer');
                resultContainer.innerHTML = '';
                console.log(data);
                for (const key of Object.keys(data)) {
                    // If empty result don't show empty table
                    if(data[key].length === 0) {
                        continue;
                    }
                    //Create tableWrapper
                    const tableWrapper = document.createElement('div');
                    tableWrapper.className = 'table_container bg-light p-2 pr-5 mb-5';
                    resultContainer.appendChild(tableWrapper);
                    tableWrapper.innerHTML =
                        '<h6 class="position-relative"> Знайдено ' + data[key].length + ' співпадіннь в базі: ' +
                        '<span class="tableName">' + key + '</span>' +
                        '<i id="wrap_table_result" class="far wrap_table_result fa-minus-square position-absolute"></i></h6>';

                    data[key].forEach(function (el) {
                        //Create table
                        const table = document.createElement('table');
                        table.className = 'w-100 table table-hover table-bordered mb-2';

                        //Create tbody
                        const tbody = document.createElement('tbody');
                        table.appendChild(tbody);
                        tableWrapper.appendChild(table);
                        for (let item in el) {
                            // tableName.innerHTML = key;
                            const tr = document.createElement('tr');
                            tbody.appendChild(tr);
                            tr.innerHTML = '<th class="w-25 bg-dark p-2 text-light columnName">' + item + '</th>' + '<td class=" p-2 ">' + el[item] + '</td>';
                        }
                    });
                }

            })
            .catch(function (error) {
                console.log('Request failed', error);
            });
    }
}

analyzer = new Analyzer();

let dropTable = document.addEventListener('click', function (e) {

    trafficName = analyzer.getTrafficName(e);
    // Method deleteTrafficTable will init if target has .delete_traffic
    if (trafficName !== undefined && e.target.classList.contains('delete_traffic')) {
        let i = e.target;
        i.classList.remove('trash');
        const iTag = document.createElement('i');
        iTag.classList.add('fa', 'fa-spinner', 'fa-spin');
        i.appendChild(iTag);
        setTimeout(function () {
            i.parentNode.parentNode.classList.add('hide');
            analyzer.deleteTrafficTable(trafficName)
        }, 2000);
    }
});

let analyze = document.addEventListener('click', function (e) {
    trafficName = analyzer.getTrafficName(e);
    if (trafficName !== undefined && e.target.classList.contains('analyze_traffic')) {

        //Remove check icon
        let analyzeBtns = document.getElementsByClassName('analyze_traffic');
        for(let i = 0; i < analyzeBtns.length; i++){
            if (analyzeBtns[i].childNodes[1].classList.contains('fa-check')){
                analyzeBtns[i].childNodes[1].classList.remove('fa-check');
                analyzeBtns[i].childNodes[1].classList.add('fa-search');
            } else {
                continue;
            }
        }
        // start preload image on btn
        let analyzeBtn = e.target;
        analyzeBtn.childNodes[1].classList.remove('fa-search');
        analyzeBtn.childNodes[1].classList.add('fa-spin');
        analyzeBtn.childNodes[1].classList.add('fa-spinner');

        analyzer.analyzeTraffic(trafficName);
        let mapBtnValue = document.getElementById('showAllConnections');
        //define value to launch map btn
        mapBtnValue.value = trafficName;
        // Compare phones from traffic with helix dbs
        analyzer.getresultFromTraffic(trafficName, analyzeBtn);
    }

});

let reactTable = document.getElementById('tb-search').addEventListener('input', function () {
    let tbSearch = document.getElementById('tb-search').value;
    analyzer.reactTable();
});

let launchMap = document.getElementById('showAllConnections').addEventListener('click', function () {
    tableName = document.getElementById('showAllConnections').value;
    data = analyzer.getAllConnections(tableName, function (data) {
        let mapWrapper = document.getElementById('map_wrapper');
        let createDiv = document.createElement('div');
        createDiv.setAttribute("id", "sb_map");
        mapWrapper.appendChild(createDiv);
        analyzer.launchMap(data);
    });

});

let closeMap = analyze = document.addEventListener('click', function (e) {

    if (e.target !== undefined && e.target.classList.contains('close_map')) {
        let map = document.getElementById('sb_map');
        map.remove();
    }
});

//Open/close result (switch plus/minus)
document.addEventListener('click', function (e) {
    if (e.target && e.target.id === 'wrap_table_result') {//do something}
        if (e.target.classList.contains('fa-minus-square')) {
            let i = e.target;
            i.classList.remove('fa-minus-square');
            i.classList.add('fa-plus-square');

            let tables = i.parentElement.parentElement.getElementsByTagName('table');
            console.log(tables.length);

            for (let i = 0; i < tables.length; i++) {
                tables[i].classList.add('d-none');
            }
        } else {
            let i = e.target;
            i.classList.remove('fa-plus-square');
            i.classList.add('fa-minus-square');

            let tables = i.parentElement.parentElement.getElementsByTagName('table');
            console.log(tables.length);

            for (let i = 0; i < tables.length; i++) {
                tables[i].classList.remove('d-none');
            }
        }
    }
});