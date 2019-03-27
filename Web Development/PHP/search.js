//This allows the user to search through the table by specifying different columns
function search() {
    searchId   = document.getElementById("searchId").value;
    searchName = document.getElementById("searchName").value;
    searchQuant = document.getElementById("searchQuant").value;
    searchPrice = document.getElementById("searchPrice").value;
    searchDate = document.getElementById("searchDate").value;
    searchUid = document.getElementById("searchUid").value;

    if(searchName == "") {
        searchName = "*";
    }

    query = "gettable.php?name=" + searchName;

    if(searchId) {
        query += "&id=" + searchId;
    }

    if(searchQuant) {
        query += "&quantity=" + searchQuant;
    }

    if(searchPrice) {
        query += "&price=" + searchPrice;
    }

    if(searchDate) {
        query += "&date=" + searchDate;
    }

    if(searchUid) {
        query += "&by=" + searchUid;
    }

    if(window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200) {
            document.getElementById("dataTable").innerHTML = this.responseText;
        }
    }
    xmlhttp.open("GET", query, true);
    xmlhttp.send();
}

