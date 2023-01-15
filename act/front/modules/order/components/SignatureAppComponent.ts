import { Component, Inject, NgZone, OnInit, EventEmitter, HostListener } from '@angular/core';


import 'rxjs/add/observable/interval';
import { IOrdersViewModelMaster } from '../view-models/interfaces/IOrdersViewModelMaster';

@Component({
    selector: 'signature-app',
    templateUrl: './SignatureAppComponent.html',
    styleUrls: ['./OrdersComponent.css'],
})

export class SignatureAppComponent implements OnInit {

    title = 'Signature Eacte';
    vm: IOrdersViewModelMaster;
    constructor(@Inject('IOrdersViewModelMaster') mainViewModel: IOrdersViewModelMaster,
        private zone: NgZone,
    ) { this.vm = mainViewModel; }
    ngOnInit() {
        this.vm.getData();


        var dropzoneId = "dropzone";
        window.addEventListener("dragenter", function (e) {
            if (e.target["id"] != dropzoneId) {
                e.preventDefault();
                e.dataTransfer.effectAllowed = "none";
                e.dataTransfer.dropEffect = "none";
            }
        }, false);

        window.addEventListener("dragover", function (e) {
            if (e.target["id"] != dropzoneId) {
                e.preventDefault();
                e.dataTransfer.effectAllowed = "none";
                e.dataTransfer.dropEffect = "none";
            }
        });

        window.addEventListener("drop", function (e) {
            if (e.target["id"] != dropzoneId) {
                e.preventDefault();
                e.dataTransfer.effectAllowed = "none";
                e.dataTransfer.dropEffect = "none";
            }
        });
    }

    
}