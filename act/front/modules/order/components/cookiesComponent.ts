import { Component, OnInit, AfterViewInit,Input } from '@angular/core';
import { MessageService } from 'primeng/api';
import { IOrdersViewModelMaster } from '../view-models/interfaces/IOrdersViewModelMaster';
import { Cookies } from '../../../shared/entities/cookies';

declare const $: any;

@Component({
    selector: 'cookiesComponent',
    templateUrl: './cookiesComponent.html',
    providers: [MessageService],
    styleUrls: ['./cookiesComponent.css'],

})

export class cookiesComponent implements OnInit, AfterViewInit {

    displayCookies: boolean = true;
    displayCookiesPrametres: boolean = false;
    cookiesInofmration: Cookies;
    acctpePiwic: boolean = false;
    displayCookiesModal: boolean = false;
    @Input() vm: IOrdersViewModelMaster;

    ngOnInit(): void {
        let getCookies = this.vm.services.orderService.getCookies();
        Promise.all([getCookies]).then(results => {
            if ((this.vm.services.cookiesService.get('assp-cookies'))) {
                this.displayCookiesModal = results[0];
            }else{
                this.displayCookiesModal=true;
            }

        });

    }

    checkBrowser(): string {
        switch (true) {
            case window.navigator.userAgent.toLowerCase().indexOf("edge") > -1: return "edge";
            case window.navigator.userAgent.toLowerCase().indexOf("edg") > -1: return "chromium based edge (dev or canary)";
            case window.navigator.userAgent.toLowerCase().indexOf("opr") > -1: return "opera";
            case window.navigator.userAgent.toLowerCase().indexOf("chrome") > -1: return "chrome";
            case window.navigator.userAgent.toLowerCase().indexOf("trident") > -1: return "ie";
            case window.navigator.userAgent.toLowerCase().indexOf("firefox") > -1: return "firefox";
            case window.navigator.userAgent.toLowerCase().indexOf("safari") > -1: return "safari";
            default: return "other";
        }
    }
    get ifCookiesSaved() {
        let result=false
        let getCookies = this.vm.services.orderService.getCookies();
        Promise.all([getCookies]).then(results => {
            result= this.displayCookiesModal = results[0];
        });
        return result;
    }

    saveCookies() {

        this.cookiesInofmration = new Cookies();
        this.cookiesInofmration.date = new Date().toUTCString();
        this.cookiesInofmration.guid = this.createGuid();
        this.cookiesInofmration.navigateur = this.checkBrowser();
        this.cookiesInofmration.piwikIgnore = this.acctpePiwic;
        let saveCookies = this.vm.services.orderService.postCookies(this.cookiesInofmration);
        Promise.all([saveCookies]).then(results => {
            this.vm.services.cookiesService.set('assp-cookies', JSON.stringify(this.cookiesInofmration), 365, null,null, null);
            this.displayCookies = false;
            this.displayCookiesModal = false;
            this.displayCookiesPrametres = false;
        });
    }
    getCookies() {
        this.vm.services.cookiesService.get('');
    }
    createGuid() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }
    ngAfterViewInit(): void {
        $('body').on('hidden.bs.modal', '.modal', function () {
            $('.add-form-actions button').each(function (index, el) {
                $(el).blur();
            });
        });
    }
}