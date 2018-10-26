// import * as  IMask from '../../resourses/assets/libs/imask/imask.js';
class formHandler {

    constructor() {
        this.phoneMask = new IMask(
            document.getElementById('phone'), {
                mask: '+{38}(000)000-00-00'
            });

        this.dateMask = new IMask(
            document.getElementById('dob'),
            {
                mask: Date,
                min: new Date(1900, 0, 1),
                max: new Date(2050, 0, 1),
                lazy: false
            });

        this.formError = document.getElementById('error_field');

    }

    getName() {
        let name;
        name = document.getElementById('name').value.toUpperCase();
        return name;
    }

    getSurname() {
        let surname;
        surname = document.getElementById('surname').value.toUpperCase();
        return surname;
    }

    getPatronymic() {
        let patronymic;
        patronymic = document.getElementById('patronymic').value.toUpperCase();
        return patronymic;
    }

    getDob() {
        let dob;
        dob = document.getElementById('dob').value.replace(/_|-/g, '').toString();
        if (dob.length > 2) {
            return dob;
        } else {
            return '';
        }
    }

    getPhone() {
        let phone;
        phone = document.getElementById('phone').value.replace(/\D/g, '').toString();
        return phone;
    }

    getPersonalId() {
        let ipn;
        ipn = document.getElementById('ipn').value.toString();
        return ipn;
    }

    getSelectedTableName() {
        let selectedTableName;
        selectedTableName = document.getElementById('dbselect').value;
        return selectedTableName;
    }

    formDataAjaxPostRequest() {
        fetch('/ajax/search', {
            method: 'post',
            headers: {
                "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
            },
            body: 'name=' + this.getName() + '&' +
                'surname=' + this.getSurname() + '&' +
                'patronymic=' + this.getPatronymic() + '&' +
                'dob=' + this.getDob() + '&' +
                'phone=' + this.getPhone() + '&' +
                'ipn=' + this.getPersonalId() + '&' +
                'tableid=' + this.getSelectedTableName()
        })
            .then(function (res) {
                document.getElementById('search_icon').classList.remove('fa-search');
                document.getElementById('search_icon').classList.add('fa-spinner');
                document.getElementById('search_icon').classList.add('fa-spin');
                return res.json();
            })
            .then(function (data) {
                let resultContainer = document.getElementById('search');
                resultContainer.innerHTML = '';
                console.log(data);
                for (const key of Object.keys(data)) {
                    // If empty result don't show empty table
                    if (data[key].length === 0) {
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
            .then(function () {
                document.getElementById('search_icon').classList.add('fa-search');
                document.getElementById('search_icon').classList.remove('fa-spinner');
                document.getElementById('search_icon').classList.remove('fa-spin');
            })
            .catch(function (error) {
                console.log('Request failed', error);
            });

    }

    refreshFormFields() {
        let fields = document.getElementsByClassName('search_field_input');
        let result = [];
        for (let i = 0; i < fields.length; i++) {
            let val = fields[i].getAttribute('data-field');
            result.push(val);
        }
        let status = document.getElementById('dbselect').value;
        if (status === '0') {
            result.forEach(function (el) {
                document.getElementById(el).disabled = false;
            });
        }
    }

    compareFieldsTable(id) {
        fetch('/ajax/getfields', {
            method: 'post',
            headers: {
                "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
            },
            body: 'id=' + id
        }).then(function (res) {
            return res.json();
        })
            .then(function (data) {
                //Get all inputs id from searchform
                let fields = document.getElementsByClassName('search_field_input');
                let result = [];
                for (let i = 0; i < fields.length; i++) {
                    let val = fields[i].getAttribute('data-field');
                    result.push(val);
                }

                // Get all fields from selected table
                let tableName = [];
                for (let i = 0; i < data.length; i++) {
                    tableName.push(data[i].COLUMN_NAME);
                }
                result.forEach(function (el) {
                    if (!tableName.includes(el)) {
                        document.getElementById(el).disabled = true;
                        document.getElementById(el).value = '';
                    } else {
                        document.getElementById(el).disabled = false;
                    }
                });

            })
            .catch(function (error) {
                console.log('Request failed', error);
            });
    }

    checkEmptyInputs() {
        if (this.getSurname().length === 0 && this.getName().length === 0 && this.getPatronymic().length === 0
            && this.getPhone().length === 0 && this.getPersonalId().length === 0) {
            return false;
        } else {
            return true;
        }
    }

    saveResult(data) {
        // fetch('/download', {
        //     method: 'post',
        //     headers: {
        //         "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
        //     },
        //     body: 'saveResult=' + data
        // }).then(function () {
        //     document.location = '/download';
        // });


            $.ajax({
                url: '/download',
                type: 'post',
                data: {saveResult: '1'},
                success: function(response){
                    window.location = response;
                }
            });

    }
}

// Init masks on form inputs
let start = new formHandler();

// Get data from DB and display result
let sendAjaxRequest = document.getElementById('search_btn').addEventListener('click', function (e) {
    e.preventDefault();
    let formHendler = new formHandler();
    if (formHendler.checkEmptyInputs() === true) {
        formHendler.formDataAjaxPostRequest();
        formHendler.formError.innerHTML = '';
        let saveIntoXls = document.getElementById('saveIntoXls');
        saveIntoXls.style.display = 'block';

    } else {
        formHendler.formError.classList.add('bg-danger');
        formHendler.formError.classList.add('text-center');
        formHendler.formError.innerHTML = '<strong>' + 'Заповніть хоча б одне поле!' + '</strong>';
    }
    let resultContainer = document.getElementById('search');
    resultContainer.classList.add('savebtn');
});

// Display only available fields
let getTableFields = document.getElementById('dbselect').addEventListener('change', function () {
    let id = document.getElementById('dbselect').value;
    let formHendler = new formHandler();
    id !== '0' ? formHendler.compareFieldsTable(id) : '';
});

//Clear search form fields
let refreshInputFields = document.getElementById('dbselect').addEventListener('change', function () {
    let formHendler = new formHandler();
    formHendler.refreshFormFields();
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

// let table = document.getElementById('tableTest');
// console.log(table);
//
// let workbook = XLSX.utils.book_new();
// let wb1 = XLSX.utils.table_to_sheet(document.getElementById('tableTest'), {sheet: "Some sheet"});
// XLSX.utils.book_append_sheet(workbook, wb1, "Sheet2");
// let wb2 = XLSX.utils.table_to_sheet(document.getElementById('tableTest1'), {sheet: "Some sheet"});
// XLSX.utils.book_append_sheet(workbook, wb2, "Sheet3");
//
//
// let wbout = XLSX.write(workbook, {bookType:'xlsx', bookSST:true,type:'binary'});
//
//
// function s2ab(s) {
//     let buf = new ArrayBuffer(s.length);
//     let view = new Uint8Array(buf);
//     for(let i = 0; i < s.length; i++){
//         view[i] = s.charCodeAt(i) & 0xFF;
//     }
//     return buf;
// }

let saveResult = document.getElementById('saveIntoXls').addEventListener('click', function () {
        start.saveResult('1');
});
