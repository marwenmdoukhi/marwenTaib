"use strict";
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
Object.defineProperty(exports, "__esModule", { value: true });
const core_1 = require("@angular/core");
const platform_browser_1 = require("@angular/platform-browser");
const forms_1 = require("@angular/forms");
const http_1 = require("@angular/http");
const common_1 = require("@angular/common");
const animations_1 = require("@angular/platform-browser/animations");
const ngx_toastr_1 = require("ngx-toastr");
const acte_services_1 = require("../../shared/services/acte.services");
const document_services_1 = require("../../shared/services/document.services");
const dialog_1 = require("primeng/dialog");
const table_1 = require("primeng/table");
const paginator_1 = require("primeng/paginator");
const tabmenu_1 = require("primeng/tabmenu");
const checkbox_1 = require("primeng/checkbox");
const ngx_treeview_1 = require("ngx-treeview");
const primeng_1 = require("primeng/primeng");
const primeng_2 = require("primeng/primeng");
const OrdersViewModelMaster_1 = require("./view-models/bases/OrdersViewModelMaster");
const CreateOrderComponent_1 = require("./components/CreateOrderComponent");
const OrdersComponent_1 = require("./components/OrdersComponent");
const app_component_1 = require("./components/app.component");
const primeng_3 = require("primeng/primeng");
const api_1 = require("primeng/api");
const steps_1 = require("primeng/steps");
const toast_1 = require("primeng/toast");
const tabview_1 = require("primeng/tabview");
const codehighlighter_1 = require("primeng/codehighlighter");
const fileupload_1 = require("primeng/fileupload");
const radiobutton_1 = require("primeng/radiobutton");
const signataire_services_1 = require("../../shared/services/signataire.services");
const avocat_services_1 = require("../../shared/services/avocat.services");
const CreateSignataireComponent_1 = require("./components/CreateSignataireComponent");
const consultActComponent_1 = require("./components/consultActComponent");
const http_2 = require("@angular/common/http");
const CreateAvocatComponent_1 = require("./components/CreateAvocatComponent");
const SignatureComponent_1 = require("./components/SignatureComponent");
const angular2_signaturepad_1 = require("angular2-signaturepad");
const FilterPipe_1 = require("./pipe/FilterPipe ");
const SignatureAppComponent_1 = require("./components/SignatureAppComponent");
const SignatureServiceInjector_1 = require("./SignatureServiceInjector");
let OrdersModule = class OrdersModule {
};
OrdersModule = __decorate([
    core_1.NgModule({
        imports: [angular2_signaturepad_1.SignaturePadModule, http_2.HttpClientModule, radiobutton_1.RadioButtonModule, dialog_1.DialogModule, codehighlighter_1.CodeHighlighterModule, tabview_1.TabViewModule, toast_1.ToastModule, animations_1.BrowserAnimationsModule, primeng_3.MenuModule, steps_1.StepsModule,
            animations_1.BrowserAnimationsModule, forms_1.FormsModule, common_1.CommonModule, forms_1.FormsModule, primeng_1.MultiSelectModule, ngx_treeview_1.TreeviewModule.forRoot(),
            fileupload_1.FileUploadModule, forms_1.ReactiveFormsModule, http_1.HttpModule, primeng_1.CalendarModule, forms_1.FormsModule, table_1.TableModule, checkbox_1.CheckboxModule, paginator_1.PaginatorModule, tabmenu_1.TabMenuModule, ngx_toastr_1.ToastrModule.forRoot(), common_1.CommonModule, platform_browser_1.BrowserModule, forms_1.FormsModule, forms_1.ReactiveFormsModule, http_1.HttpModule, animations_1.BrowserAnimationsModule, ngx_toastr_1.ToastrModule.forRoot(),
        ],
        declarations: [SignatureAppComponent_1.SignatureAppComponent, FilterPipe_1.FilterPipe, SignatureComponent_1.SignatureComponent, CreateAvocatComponent_1.CreateAvocatComponent, CreateSignataireComponent_1.CreateSignataireComponent, app_component_1.AppComponent, OrdersComponent_1.OrdersComponent, CreateOrderComponent_1.CreateOrderComponent, consultActComponent_1.consultActComponent],
        providers: [SignatureServiceInjector_1.SignatureServiceInjector, avocat_services_1.AvocatService, signataire_services_1.SignataireService, api_1.MessageService, primeng_1.CalendarModule, acte_services_1.ActeService, document_services_1.DocumentService, primeng_2.Calendar, fileupload_1.FileUploadModule,
            { provide: 'IOrdersViewModelMaster', useClass: OrdersViewModelMaster_1.OrdersViewModelMaster },
        ],
        bootstrap: [app_component_1.AppComponent]
    })
], OrdersModule);
exports.OrdersModule = OrdersModule;
