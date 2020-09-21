<?php

declare(strict_types=1);

namespace AndrewSvirin\Ebics\Tests\Models;

use AndrewSvirin\Ebics\Models\Certificate;
use PHPUnit\Framework\TestCase;

class CertificateTest extends TestCase
{
    public function testGetter(): void
    {
        $content = '-----BEGIN CERTIFICATE-----
MIIFkTCCBPqgAwIBAgIgATFR4s38D0w+myn6PrQZ5hxApNXHM0ZG9gj+tvD3TEQw
DQYJKoZIhvcNAQELBQAwRDELMAkGA1UEBgwCVVMxFjAUBgNVBAoMDUdlb1RydXN0
IEluYy4xHTAbBgNVBAMMFEdlb1RydXN0IFNTTCBDQSAtIEczMB4XDTIwMDMyMTAw
MDAwMFoXDTIxMDMyMjAwMDAwMFowazELMAkGA1UEBgwCRlIxFzAVBgNVBAgMDlNl
aW5lLWV0LU1hcm5lMQ4wDAYDVQQHDAVNZWx1bjEdMBsGA1UECgwURWxjaW1haSBJ
bmZvcm1hdGlxdWUxFDASBgNVBAMMCyoud2ViYW5rLmZyMIGfMA0GCSqGSIb3DQEB
AQUAA4GNADCBiQKBgQCMwexPODeJcwskuyIjIqQ2pDkI6k4HEVnpfGOdc4x9jF0c
FYn4pdwJ9Mdz6GqgoHLjWH2D1rKH1jEsOFT9ks+QyHRtKG/q9lyCrzuBo6cYTXU8
Mgi9USM+Z70J4NVSFKObOCz/3eJrz4fDe955DEMqhc+VkmXlyOOdiKy7Pi2bbwID
AQABo4IDSzCCA0cwHQYDVR0OBBYEFEQCpSXQ8qCNe7wAST1u7l+xWfk3MCEGA1Ud
EQQaMBiCCyoud2ViYW5rLmZyggl3ZWJhbmsuZnIwCQYDVR0TBAIwADAOBgNVHQ8B
Af8EBAMCBaAwKwYDVR0fBCQwIjAgoB6gHIYaaHR0cDovL2duLnN5bWNiLmNvbS9n
bi5jcmwwgZ0GA1UdIASBlTCBkjCBjwYGZ4EMAQICMIGEMD8GCCsGAQUFBwIBFjNo
dHRwczovL3d3dy5nZW90cnVzdC5jb20vcmVzb3VyY2VzL3JlcG9zaXRvcnkvbGVn
YWwwQQYIKwYBBQUHAgIwNQwzaHR0cHM6Ly93d3cuZ2VvdHJ1c3QuY29tL3Jlc291
cmNlcy9yZXBvc2l0b3J5L2xlZ2FsMB0GA1UdJQQWMBQGCCsGAQUFBwMBBggrBgEF
BQcDAjBXBggrBgEFBQcBAQRLMEkwHwYIKwYBBQUHMAGGE2h0dHA6Ly9nbi5zeW1j
ZC5jb20wJgYIKwYBBQUHMAKGGmh0dHA6Ly9nbi5zeW1jYi5jb20vZ24uY3J0MIIB
gAYKKwYBBAHWeQIEAgSCAXAEggFsAWoAdwDd6x0reg1PpiCLga2BaHB+Lo6dAdVc
iI09EcTNtuy+zAAAAV0IlzKdAAAEAwBIMEYCIQCAmBAT3jrrGQzLqiWr7XG9Ma31
E5dyCZ1QiG3gQTiDXgIhANk2NhwptVTq1o3+6efZYeWwOCmEyBBfMAse3u+sj9si
AHYApLkJkLQYWBSHuxOizGdwCjw1mAT5G9+443fNDsgN3BAAAAFdCJcy0AAABAMA
RzBFAiBPH7a68j1F5NiI70iLzmqh63V1z7LnxFPjAA70Lg4JqQIhAJGE7aQlbF5J
8/Uvb9DbgjcwYsyf/+bCF0njea7h72fyAHcA7ku9t3XOYLrhQmkfq+GeZqMPfl+w
ctiDAMR7iXqo/csAAAFdCJc0lwAABAMASDBGAiEAz50rc4sEvmcbOn89K3fJFpvz
kAPePPr2DlOoZ2sy6GQCIQCcMy79mKrIFY7f6WLcv3+GLcFwdfvisCYDc5fWM3Eg
oTAfBgNVHSMEGDAWgBREAqUl0PKgjXu8AEk9bu5fsVn5NzANBgkqhkiG9w0BAQsF
AAOBgQAxGXciJF/M2YjL0bGlTnY6kWXLycc/7Jinid9wed+5DiTzBFaVDyOzVAMl
r5tsKbt8WSCVQ8X5Sj9rfUzTm0bZgYpkUPgeGCjygvNSSwX06Z5gvO22Dl7FwBuQ
qMhfNZS+QyoxgBs18dvl82RtCY8EZXP/jMdHu1gHlFQD6wZyHQ==
-----END CERTIFICATE-----';

        $sUT = new Certificate('test', 'test2');

        self::assertSame('test', $sUT->getType());
        self::assertSame('test2', $sUT->getPublicKey());
        self::assertNull($sUT->getPrivateKey());
        self::assertNull($sUT->getContent());
        self::assertNull($sUT->toX509());

        $sUT = new Certificate('test', 'test2', 'test3');

        self::assertSame('test', $sUT->getType());
        self::assertSame('test2', $sUT->getPublicKey());
        self::assertSame('test3', $sUT->getPrivateKey());
        self::assertNull($sUT->getContent());
        self::assertNull($sUT->toX509());

        $sUT = new Certificate('test', 'test2', 'test3', $content);

        self::assertSame('test', $sUT->getType());
        self::assertSame('test2', $sUT->getPublicKey());
        self::assertSame('test3', $sUT->getPrivateKey());
        self::assertSame($content, $sUT->getContent());

        self::assertEquals('GeoTrust SSL CA - G3', $sUT->toX509()->getInsurerName());
        self::assertEquals('539453510852155194065233908413342789156542395956670254476154968597583055940', $sUT->toX509()->getSerialNumber());
    }
}
