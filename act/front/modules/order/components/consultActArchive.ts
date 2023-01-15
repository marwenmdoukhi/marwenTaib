import { Component, OnInit, AfterViewInit, trigger, transition, style, animate, state, Input } from '@angular/core';
import { MessageService } from 'primeng/api';
import { OrdersViewModelMaster } from '../view-models/bases/OrdersViewModelMaster';
import { Order } from '../../../shared/entities/order';
import { IOrdersViewModelMaster } from '../view-models/interfaces/IOrdersViewModelMaster';
import { DomSanitizer } from '@angular/platform-browser';
import { Signataire } from '../../../shared/entities/signataire';
import { Document } from "../../../shared/entities/document";
import { Avocat } from "../../../shared/entities/avocat";
import { User } from "../../../shared/entities/user";
import * as moment from "moment";
import {NgxPaginationModule} from 'ngx-pagination';


@Component({
    selector: 'consultActArchive',
    templateUrl: './consultActArchive.html',
    providers: [MessageService],
    styleUrls: ['./consultActArchive.css'],
    animations: [
        trigger(
            'slideView',
            [
                state('true', style({ transform: 'translateX(100%)', opacity: 0 })),
                state('false', style({ transform: 'translateX(0)', opacity: 1 })),
                transition('0 => 1', animate('500ms', style({ transform: 'translateX(0)', 'opacity': 1 }))),
                transition('1 => 1', animate('500ms', style({ transform: 'translateX(100%)', 'opacity': 0 }))),
            ]),

        trigger('slideInOut', [
            transition(':enter', [
                style({ transform: 'translateX(100%)', opacity: 0 }),
                animate('600ms ease-in', style({ transform: 'translateX(0%)', 'opacity': 1 }))
            ]),

            transition(':leave', [
                style({ transform: 'translateX(0%)', opacity: 1 }),
                animate('0ms ease-in', style({ transform: 'translateX(100%)', 'opacity': 0 }))
            ])
        ])
    ]

})

export class ConsultActArchive implements OnInit{
    @Input() vm: IOrdersViewModelMaster;
    readyOnlyForUser :boolean = false;
    p : number = 1;
    actArchives = [];

    ngOnInit(): void {
        this.getActArchive();
    }

    set displayModalSignataire(displayModalSignataire: boolean) {
        OrdersViewModelMaster.displayModalSignataire = displayModalSignataire;
    }

    get displayModalSignataire(): boolean {
        return OrdersViewModelMaster.displayModalSignataire;
    }
    get displayModalAvocat(): boolean {
        return OrdersViewModelMaster.displayModalAvocat;
    }
    set displayModalAvocat(displayModalAvocat: boolean) {
        OrdersViewModelMaster.displayModalAvocat = displayModalAvocat;
    }

    set displayCreateOrder(displayCreateOrder: boolean) {
        OrdersViewModelMaster.displayCreateOrder = displayCreateOrder;
    }

    get displayCreateOrder(): boolean {
        return OrdersViewModelMaster.displayCreateOrder;
    }


    set displayConsultOrder(displayConsultOrder: boolean) {
        OrdersViewModelMaster.displayConsultOrder = displayConsultOrder;
    }

    get displayConsultOrder(): boolean {
        return OrdersViewModelMaster.displayConsultOrder;
    }

    get currentActe(): Order {
        return OrdersViewModelMaster.currentActe;
    }
    get userConnected(): User {
        return OrdersViewModelMaster.userConnected;
    }


    getActArchive(){
        let getArchive = this.vm.services.orderService.getActArchive(this.currentActe.id);
        Promise.all([getArchive]).then(result=>{
            this.actArchives = result[0];
            this.actArchives.forEach(function (value) {
                value.actionDate.date = moment(value.actionDate.date).format('DD/MM/YYYY HH:mm');
                console.log(value.actionDate.date);
                // debugger;
            });
            // let array = result[0].map(Object.values);
            // this.actArchives.push(result[0][0]);
            // console.log(result[0][0])
        });
    }

}