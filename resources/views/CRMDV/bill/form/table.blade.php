<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        margin: 20px 0;
    }

    th, td {
        border: 1px solid #ddd;
        text-align: left;
        padding: 0;
    }

    th {
        background-color: #f2f2f2;
        height: 20px;
    }
    tr{
        height: 40px;
        margin: 10px 0;
    }

    input {
        width: 100%;
        height: 100%;
        box-sizing: border-box;
        border: none;
        padding: 8px; /* Nếu muốn thêm padding */
    }

    button {
        padding: 8px;
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
    }

    button:hover {
        background-color: #45a049;
    }

    tfoot {
        font-weight: bold;
    }

    #total {
        font-weight: bold;
    }
</style>

<table id="myTable">
    <thead>
    <tr>
        <th>STT</th>
        <th>Hạng mục</th>
        <th>Đơn giá</th>
        <th>Số lượng</th>
        <th>Thành tiền</th>
        <th>Xoá</th>
    </tr>
    </thead>
    <tbody>
    <!-- Bảng sẽ được điều chỉnh thông qua JavaScript -->
    </tbody>
    <tfoot>
    <tr>
        <td colspan="4" style="text-align: right;">Tổng tiền:</td>
        <td id="total">0</td>
        <td></td>
    </tr>
    </tfoot>
</table>

<button onclick="addRow()">Thêm Dòng</button>

<script>
    var rowCounter = 1; // Biến đếm số thứ tự
    var tableDataArray = []; // Mảng để lưu trữ dữ liệu từ bảng

    function addRow() {
        var table = document.getElementById("myTable").getElementsByTagName('tbody')[0];
        var newRow = table.insertRow(table.rows.length);
        var cells = [];

        cells[0] = newRow.insertCell(0);
        cells[0].innerHTML = rowCounter++;

        for (var i = 1; i < 5; i++) {
            cells[i] = newRow.insertCell(i);
            var input = document.createElement("input");
            input.type = "text";
            input.name = "row_text[]";

            if (i === 2 || i === 3) { // Đơn giá và Số lượng là input number
                input.type = "number";
                input.value = 0;
                input.name = "row_" + (i === 2 ? "price[]" : "quantity[]");
                input.addEventListener("input", function() {
                    updateRow(newRow);
                    updateTotal();
                    updateTableDataArray();
                });
            }
            cells[i].appendChild(input);
        }

        var deleteCell = newRow.insertCell(5);
        var deleteButton = document.createElement("button");
        deleteButton.innerHTML = "Xoá";
        deleteButton.onclick = function() {
            deleteRow(newRow);
            updateTotal();
            updateTableDataArray();
        };
        deleteCell.appendChild(deleteButton);

        updateRow(newRow);
        updateTotal();
        updateTableDataArray();
    }


    function deleteRow(row) {
        var table = document.getElementById("myTable").getElementsByTagName('tbody')[0];
        table.deleteRow(row.rowIndex - 1); // rowIndex is 1-based
    }

    function updateRow(row) {
        var cells = row.getElementsByTagName('td');
        var quantity = parseFloat(cells[3].getElementsByTagName('input')[0].value);
        var price = parseFloat(cells[2].getElementsByTagName('input')[0].value);
        var totalPrice = isNaN(quantity) || isNaN(price) ? 0 : (quantity * price);
        cells[4].innerHTML = totalPrice.toFixed(2);
    }

    function updateTotal() {
        var table = document.getElementById("myTable").getElementsByTagName('tbody')[0];
        var totalCell = document.getElementById("total");
        var total = 0;

        for (var i = 0; i < table.rows.length; i++) {
            var rowCells = table.rows[i].getElementsByTagName('td');
            var subtotal = parseFloat(rowCells[4].innerHTML);
            total += isNaN(subtotal) ? 0 : subtotal;
        }

        totalCell.innerHTML = total.toFixed(2);
    }
    function updateTableDataArray() {
        tableDataArray = [];

        var table = document.getElementById("myTable").getElementsByTagName('tbody')[0];

        for (var i = 0; i < table.rows.length; i++) {
            var rowCells = table.rows[i].getElementsByTagName('td');
            var rowData = {
                'stt': rowCells[0].innerHTML,
                'category': rowCells[1].getElementsByTagName('input')[0].value,
                'price': rowCells[2].getElementsByTagName('input')[0].value,
                'quantity': rowCells[3].getElementsByTagName('input')[0].value,
                'total': rowCells[4].innerHTML
            };
            tableDataArray.push(rowData);
        }

        console.log(tableDataArray);
    }

</script>