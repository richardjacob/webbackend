app.controller("reports",["$scope","$http",function(r,t){r.report=function(o,e,s){t.post(APP_URL+"/admin/reports",{from:o,to:e,category:s}).then(function(t){r.category||(r.users_report=t.data,r.rooms_report=!1,r.reservations_report=!1),"rooms"==r.category&&(r.users_report=!1,r.rooms_report=t.data,r.reservations_report=!1),"reservations"==r.category&&(r.users_report=!1,r.rooms_report=!1,r.reservations_report=t.data)})},r.print=function(r){r=r?r:"users";var t=document.getElementById(r),o=window.open("","","left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0");o.document.write(t.innerHTML),o.document.close(),o.focus(),o.print(),o.close()},$(".date").datepicker({dateFormat:"dd-mm-yy"})}]);