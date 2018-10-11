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

    launchMap() {

        let map = L.map('sb_map').setView([48.35, 39.20], 10);

        let TopoLayer = L.tileLayer('/public/map-tiles/{z}/{x}/{y}.png', {
            maxZoom: 9
        });
        map.addLayer(TopoLayer);
        document.getElementById('sb_map').style.display = 'block';
        setTimeout(function () {
            map.invalidateSize()
        }, 400);
    }

    getAllConnections(tableName,ressolve) {
        fetch('/ajax/getallconnections', {
            method: 'post',
            headers: {
                "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
            },
            body: 'getallconnections=' + tableName
        }).then(function (res) {
            ressolve(res.json());
            // console.log(res.json());
        });
    }

    drawMarkers(data){
        // Creating a marker

        this.launchMap();
        let marker = L.marker([48.35, 39.20]);

        // Adding marker to the map
        marker.addTo(this.map);
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
        analyzer.analyzeTraffic(trafficName);
        let mapBtnValue = document.getElementById('showAllConnections');
        mapBtnValue.value = trafficName;
    }

});

let reactTable = document.getElementById('tb-search').addEventListener('input', function () {
    let tbSearch = document.getElementById('tb-search').value;
    analyzer.reactTable();
});

let launchMap = document.getElementById('showAllConnections').addEventListener('click', function () {
    tableName = document.getElementById('showAllConnections').value;
    data = analyzer.getAllConnections(tableName, function (data) {
        analyzer.drawMarkers(data);
    });

});