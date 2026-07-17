import './bootstrap';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Carregando o QZ Tray
window.qz = require('qz-tray');

// CONFIGURAÇÃO DE SEGURANÇA (Obrigatória para parar o bloqueio)
qz.security.setCertificatePromise(function (resolve, reject) {
    resolve("-----BEGIN CERTIFICATE-----\n" +
        "MIIECzCCAvOgAwIBAgIGAZ9wtgNpMA0GCSqGSIb3DQEBCwUAMIGiMQswCQYDVQQG\n" +
        "EwJVUzELMAkGA1UECAwCTlkxEjAQBgNVBAcMCUNhbmFzdG90YTEbMBkGA1UECgwS\n" +
        "UVogSW5kdXN0cmllcywgTExDMRswGQYDVQQLDBJRWiBJbmR1c3RyaWVzLCBMTEMx\n" +
        "HDAaBgkqhkiG9w0BCQEWDXN1cHBvcnRAcXouaW8xGjAYBgNVBAMMEVFaIFRyYXkg\n" +
        "RGVtbyBDZXJ0MB4XDTI2MDcxNjE1MzMyNFoXDTQ2MDcxNjE1MzMyNFowgaIxCzAJ\n" +
        "BgNVBAYTAlVTMQswCQYDVQQIDAJOWTESMBAGA1UEBwwJQ2FuYXN0b3RhMRswGQYD\n" +
        "VQQKDBJRWiBJbmR1c3RyaWVzLCBMTEMxGzAZBgNVBAsMElFaIEluZHVzdHJpZXMs\n" +
        "IExMQzEcMBoGCSqGSIb3DQEJARYNc3VwcG9ydEBxei5pbzEaMBgGA1UEAwwRUVog\n" +
        "VHJheSBEZW1vIENlcnQwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDO\n" +
        "9V4p+gbX/ghtCDpfkNm+29IfKOywq56oUW8rsy6L3oUfB3h8wMNd+izyJeUea+S2\n" +
        "2XVeUYNo9OgzZAOti1cu9y07uapGU1utELrtdztXQyBpSmqqSXWv4tMCR4qmSsod\n" +
        "e2u/5tB9pf0PeknHeX4WHecizwhy41vFCSHg/l/vwD6Fpw798ZL471VC+z0ygKbV\n" +
        "qfbiuH6En/ZmxtyK/uFVYLgvf+ow287Sojb/cgdWEKML3yTUqMJ7XVgtiXA+Eoq+\n" +
        "FqeXQIU+Dixfcalav53cNyaUZXQRhi+svqODIyymw4CFUNS3xcNHGrF0I7lFv7EC\n" +
        "UEcciCWTouyMOZCImrnNAgMBAAGjRTBDMBIGA1UdEwEB/wQIMAYBAf8CAQEwDgYD\n" +
        "VR0PAQH/BAQDAgEGMB0GA1UdDgQWBBR2qZH+8POS8fW4PPHm1DSrkrmWkTANBgkq\n" +
        "hkiG9w0BAQsFAAOCAQEAmocDC/2PqMhWWtdTsGCjetk+Fxbo5z7P7hMJ07DjIIGx\n" +
        "Ly/RXAXG4F5AZ0yK/AY29fEF9NKpeqtLBJXiLUKFQt728R1i5SABv/4MaEcH/OQl\n" +
        "oJLJk/R4CXLl0OMAPm8tPv3aWFRrI4Vt+Uc9Mk/CGjEYwq8HWzEP4b17PIrG/IEU\n" +
        "S4cIcSTWRcgWxHX+AfBXsYOeWzlI5CHtRh6WMeikn4utbWzkTpZutjOYv77LgJXh\n" +
        "5783NS72evdhpAiL+XXXClsHYDl4b7ewpxqf84mFJJbdrnuG6wzjs/gFOOm+2Viv\n" +
        "lmWkWL7c/CPVdPLiXqTESlQgEn21JrHf7Sz9RIenWg==\n" +
        "-----END CERTIFICATE-----");
});