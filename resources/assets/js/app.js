import './bootstrap';
//marketing
import EmailLists from './components/emails/EmailLists.vue';
import List from './components/emails/List.vue';
import Campaign from './components/marketing/Campaign.vue';
import SendSms from './components/marketing/SendSms.vue';

//users
import CreateUserForm from './components/users/CreateUserForm.vue';
import UpdateUserForm from './components/users/UpdateUserForm.vue';

//compliance & risk
import AlertLists from './components/compliance/alerts/AlertLists.vue';
import CompilenceSettings from './components/compliance/CompilenceSettings.vue';

import Customers from './components/compliance/customers/Customers.vue';
import CustomerInfo from './components/compliance/customers/CustomerInfo.vue';


import PendingDeposits from './components/compliance/pending/PendingDeposits.vue';
//common
import Pagination from './components/common/Pagination.vue';
import Modal from './components/common/Modal.vue';
import FiltersPanel from './components/common/FiltersPanel.vue';

//sales
import Tables from './components/sales/tables/Tables.vue';
import EmployeesView from './components/sales/employees/EmployeesView.vue';
import SalesDeposits from './components/sales/deposits/SalesDeposits.vue';
import SalesWithdrawals from  './components/sales/withdrawals/SalesWithdrawals.vue';
import SalesReports from  './components/sales/reports/SalesReports.vue';
import TableReports from './components/sales/reports/TableReports.vue';


//screens

import Playground from './components/screens/playground/Playground.vue';
import ScoreboardFtd from './components/screens/scoreboard/ScoreboardFtd.vue';
import ScoreboardRst from './components/screens/scoreboard/ScoreboardRst.vue';
import WinnerVideo from './components/screens/playground/WinnerVideo.vue';

//uploader
import CreateCustomers from './components/uploader/CreateCustomers.vue';
import EditCustomers from './components/uploader/EditCustomers.vue';


//system
import SystemDeposits from './components/system/deposits/SystemDeposits.vue';
import SystemWithdrawals from './components/system/withdrawals/SystemWithdrawals.vue';

//common
Vue.component('pagination', Pagination);
Vue.component('modal', Modal);
Vue.component('filters-panel', FiltersPanel);

//emails
Vue.component('email-lists', EmailLists);
Vue.component('list', List);

//Emails
Vue.component('user-create', CreateUserForm);
Vue.component('user-edit', UpdateUserForm);

//Complience
Vue.component('alert-lists', AlertLists);
Vue.component('pending-deposits', PendingDeposits);

//Customers
Vue.component('customers', Customers);
Vue.component('customer-info', CustomerInfo);

Vue.component('complience-settings', CompilenceSettings);


//Playground & Scoreboard
Vue.component('playground', Playground);
Vue.component('scoreboard-ftd', ScoreboardFtd);
Vue.component('scoreboard-rst', ScoreboardRst);
Vue.component('winner-video', WinnerVideo);


//Uploader
Vue.component('create-customers', CreateCustomers);
Vue.component('edit-customers', EditCustomers);

//Sales
Vue.component('sales-deposits', SalesDeposits);
Vue.component('sales-withdrawals', SalesWithdrawals);
Vue.component('sales-tables', Tables);
Vue.component('sales-reports', SalesReports);
Vue.component('table-reports', TableReports);


//Employees
Vue.component('employees-view', EmployeesView);

//System
Vue.component('reports-deposits', SystemDeposits);
Vue.component('system-withdrawals', SystemWithdrawals);

//Marketing
Vue.component('campaign', Campaign);
Vue.component('send-sms', SendSms);

if(document.getElementById('app')){
    const app = new Vue({
        el: '#app'
    });
}

document.addEventListener('DOMContentLoaded', function () {
    if (Notification && Notification.permission !== "granted") {
        Notification.requestPermission();
    }
});
