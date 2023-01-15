import { Component } from '@angular/core';
import { IOrdersViewModelMaster } from '../view-models/interfaces/IOrdersViewModelMaster';
import { OrdersViewModelMaster } from '../view-models/bases/OrdersViewModelMaster';
import { SignatureServiceInjector } from '../SignatureServiceInjector';

@Component({
    selector: 'apps',
    template: `<signature-app></signature-app>`,
})
export class AppComponent {
    vm: IOrdersViewModelMaster;
    constructor(private services: SignatureServiceInjector) {
        this.vm = new OrdersViewModelMaster(services);
    }
}
