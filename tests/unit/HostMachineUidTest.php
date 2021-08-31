<?php

declare(strict_types=1);

namespace Serato\AppEvents\Test;

use Serato\AppEvents\HostMachineUid;

class HostMachineUidTest extends AbstractTestCase
{
    public function testToString()
    {
        $hostMachineUidStr = 'P57TL8GGQI69~PG796169S564N489~GBFUL623C0UIG';
        $hm = HostMachineUid::create($hostMachineUidStr);
        $this->assertEquals($hostMachineUidStr, (string)$hm);
    }

    /**
     * Tests valid host IDs
     *
     * @dataProvider validHostIdProvider
     */
    public function testValidHostIds(string $hostId)
    {
        $hm = HostMachineUid::create($hostId);
        $this->assertEquals($hostId, (string)$hm);
    }

    /**
     * Tests invalid host IDs
     *
     * @dataProvider invalidHostIdProvider
     * @expectedException Serato\AppEvents\Exception\InvalidHostMachineUidException
     */
    public function testInvalidHostIds(string $hostId)
    {
        HostMachineUid::create($hostId);
    }

    public function validHostIdProvider(): array
    {
        return [
            ['P57TL8GGQI69~'],                                                      # No storage ID
            ['P57TL8GGQI69~PG796169S564N489~'],                                     # Missing storage ID after delimiter
            ['P57TL8GGQI69~PG796169S564N489~PG796169S564N489'],                     # Duplicate storage IDs

            ['SID=P57TL8GGQI69~'],                                                  # No storage ID
            ['SID=P57TL8GGQI69~PG796169S564N489 SID=P57TL8GGQI69~'],                # Missing storage ID after delimiter
            ['SID=P57TL8GGQI69~PG796169S564N489 SID=P57TL8GGQI69~PG796169S564N489'] # Duplicate storage IDs
        ];
    }

    public function invalidHostIdProvider(): array
    {
        return [
            [''],
            ['P57TL8GGQI69'],                                                       # Incorrect number of segments
            ['P57TL8GGQI69~P57TL8GGQI69'],                                          # Duplicate system ID (1)
            ['P57TL8GGQI69~P57TL8GGQI69~PG796169S564N489'],                         # Duplicate system ID (2)

            ['SID='],
            ['SID=P57TL8GGQI69'],                                                    # Incorrect number of segments
            ['SID=P57TL8GGQI69~P57TL8GGQI69'],                                       # Duplicate system ID (1)
            ['SID=P57TL8GGQI69~P57TL8GGQI69 SID=P57TL8GGQI69~GBFUL623C0UIG'],        # Duplicate system ID (2)
            ['SID=P57TL8GGQI69~PG796169S564N489 SID=QZ7TL8GGQI69~GBFUL623C0UIG']     # Non-match system ID
        ];
    }

    /**
     * @dataProvider matchingHostIdProvider
     */
    public function testMatchingHostIds(string $hostId1, string $hostId2)
    {
        $hm = HostMachineUid::create($hostId1);
        $this->assertTrue($hm->match(HostMachineUid::create($hostId2)));
    }

    /**
     * @dataProvider matchingHostIdProvider
     */
    public function testGetCanonicalHostId(string $hostId1, string $hostId2, string $hostId3)
    {
        $hm = HostMachineUid::create($hostId2);
        $this->assertEquals($hostId3, $hm->getCanonicalHostId());
    }

    public function matchingHostIdProvider(): array
    {
        return [
            [
                'P57TL8GGQI69~',
                'P57TL8GGQI69~',
                'P57TL8GGQI69~'
            ],
            [
                'P57TL8GGQI69~',
                'P57TL8GGQI69~PG796169S564N489~',
                'P57TL8GGQI69~~PG796169S564N489'
            ],
            [
                'P57TL8GGQI69~GBFUL623C0UIG',
                'P57TL8GGQI69~GBFUL623C0UIG~GBFUL623C0UIG',
                'P57TL8GGQI69~GBFUL623C0UIG'
            ],
            [
                'P57TL8GGQI69~GBFUL623C0UIG~GBFUL623C0UIG',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489'
            ],
            [
                'P57TL8GGQI69~GBFUL623C0UIG',
                'P57TL8GGQI69~PG796169S564N489~GBFUL623C0UIG',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489'
            ],
            [
                'P57TL8GGQI69~GBFUL623C0UIG',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489'
            ],
            [
                'P57TL8GGQI69~PG796169S564N489',
                'P57TL8GGQI69~PG796169S564N489~GBFUL623C0UIG',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489'
            ],
            [
                'P57TL8GGQI69~PG796169S564N489',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489'
            ],
            [
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489'
            ],
            [
                'P57TL8GGQI69~PG796169S564N489~GBFUL623C0UIG',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489'
            ],
            [
                'SID=P57TL8GGQI69~',
                'SID=P57TL8GGQI69~ SID=P57TL8GGQI69~PG796169S564N489',
                'P57TL8GGQI69~~PG796169S564N489'
            ],
            [
                'SID=P57TL8GGQI69~GBFUL623C0UIG',
                'SID=P57TL8GGQI69~GBFUL623C0UIG SID=P57TL8GGQI69~GBFUL623C0UIG',
                'P57TL8GGQI69~GBFUL623C0UIG'
            ],
            [
                'SID=P57TL8GGQI69~GBFUL623C0UIG SID=P57TL8GGQI69~GBFUL623C0UIG',
                'SID=P57TL8GGQI69~GBFUL623C0UIG SID=P57TL8GGQI69~PG796169S564N489',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489'
            ],
            [
                'SID=P57TL8GGQI69~GBFUL623C0UIG',
                'SID=P57TL8GGQI69~GBFUL623C0UIG SID=P57TL8GGQI69~PG796169S564N489',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489'
            ],
            [
                'SID=P57TL8GGQI69~GBFUL623C0UIG',
                'SID=P57TL8GGQI69~PG796169S564N489 SID=P57TL8GGQI69~GBFUL623C0UIG',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489'
            ],
            [
                'SID=P57TL8GGQI69~PG796169S564N489',
                'SID=P57TL8GGQI69~GBFUL623C0UIG SID=P57TL8GGQI69~PG796169S564N489',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489'
            ],
            [
                'SID=P57TL8GGQI69~PG796169S564N489',
                'SID=P57TL8GGQI69~PG796169S564N489 SID=P57TL8GGQI69~GBFUL623C0UIG',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489'
            ],
            [
                'SID=P57TL8GGQI69~GBFUL623C0UIG SID=P57TL8GGQI69~PG796169S564N489',
                'SID=P57TL8GGQI69~GBFUL623C0UIG SID=P57TL8GGQI69~PG796169S564N489',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489',
            ],
            [
                'SID=P57TL8GGQI69~GBFUL623C0UIG SID=P57TL8GGQI69~PG796169S564N489',
                'SID=P57TL8GGQI69~PG796169S564N489 SID=P57TL8GGQI69~GBFUL623C0UIG',
                'P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489'
            ]
        ];
    }

    /**
     * @dataProvider nonMatchingHostIdProvider
     */
    public function testNonMatchingHostIds(string $hostId1, string $hostId2)
    {
        $hm = HostMachineUid::create($hostId1);
        $this->assertFalse($hm->match(HostMachineUid::create($hostId2)));
    }

    public function nonMatchingHostIdProvider(): array
    {
        return [
            ['P57TL8GGQI69~GBFUL623C0UIG', 'P57TL8GGQI69~PG796169S564N489'],
            ['P57TL8GGQI69~GBFUL623C0UIG-QW796169S564N477', 'P57TL8GGQI69~YXFUL623C0UPM~PG796169S564N489'],
            ['P57TL8GGQI69~GBFUL623C0UIG', 'LQ7TL8GGQI69~GBFUL623C0UIG'],
            ['P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489', 'LQ7TL8GGQI69~GBFUL623C0UIG~PG796169S564N489'],

            ['SID=P57TL8GGQI69~GBFUL623C0UIG', 'SID=P57TL8GGQI69~PG796169S564N489'],
            [
                'SID=P57TL8GGQI69~GBFUL623C0UIG SID=P57TL8GGQI69~QW796169S564N477',
                'SID=P57TL8GGQI69~YXFUL623C0UPM SID=P57TL8GGQI69~PG796169S564N489'
            ],
            ['SID=P57TL8GGQI69~GBFUL623C0UIG', 'SID=LQ7TL8GGQI69~GBFUL623C0UIG'],
            [
                'SID=P57TL8GGQI69~GBFUL623C0UIG SID=P57TL8GGQI69~PG796169S564N489',
                'SID=LQ7TL8GGQI69~GBFUL623C0UIG SID=LQ7TL8GGQI69~PG796169S564N489'
            ]
        ];
    }

    /**
     * Tests the `HostMachineUid::get` method
     *
     * @param string $rawHostId
     * @param integer $length
     * @param string $getHostId
     * @param string $getHostIdExtended
     *
     * @return void
     *
     * @dataProvider getHostIdProvider
     */
    public function testGetHostId(
        string $rawHostId,
        ?int $length,
        string $getHostId,
        string $getHostIdExtended,
        string $getHostIdOriginalFormat
    ): void {
        $hm = HostMachineUid::create($rawHostId);

        # Get as `Host ID Extended` format
        $this->assertEquals(
            $hm->get($length, true),
            $getHostIdExtended
        );
        # Get as `Host ID` format
        $this->assertEquals(
            $hm->get($length, false),
            $getHostId
        );
        # Get as same format as raw host ID
        $this->assertEquals(
            $hm->get($length),
            $getHostIdOriginalFormat
        );
    }

    public function getHostIdProvider(): array
    {
        return [
            [
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1 SID=SYSTEMID~STORAGEID2 SID=SYSTEMID~STORAGEID3',
                null,
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~STORAGEID3',
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1 SID=SYSTEMID~STORAGEID2 SID=SYSTEMID~STORAGEID3',
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1 SID=SYSTEMID~STORAGEID2 SID=SYSTEMID~STORAGEID3'
            ],
            [
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1 SID=SYSTEMID~STORAGEID2 SID=SYSTEMID~STORAGEID3',
                1,
                'SYSTEMID~STORAGEID0',
                'SID=SYSTEMID~STORAGEID0',
                'SID=SYSTEMID~STORAGEID0'
            ],
            [
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1 SID=SYSTEMID~STORAGEID2 SID=SYSTEMID~STORAGEID3',
                55,
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~STORAGEID3',
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1',
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1'
            ],
            [
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1 SID=SYSTEMID~STORAGEID2 SID=SYSTEMID~STORAGEID3',
                35,
                'SYSTEMID~STORAGEID0~STORAGEID1',
                'SID=SYSTEMID~STORAGEID0',
                'SID=SYSTEMID~STORAGEID0'
            ],
            [
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~STORAGEID3',
                null,
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~STORAGEID3',
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1 SID=SYSTEMID~STORAGEID2 SID=SYSTEMID~STORAGEID3',
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~STORAGEID3'
            ],
            [
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~STORAGEID3',
                1,
                'SYSTEMID~STORAGEID0',
                'SID=SYSTEMID~STORAGEID0',
                'SYSTEMID~STORAGEID0'
            ],
            [
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~STORAGEID3',
                55,
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~STORAGEID3',
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1',
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~STORAGEID3'
            ],
            [
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~STORAGEID3',
                35,
                'SYSTEMID~STORAGEID0~STORAGEID1',
                'SID=SYSTEMID~STORAGEID0',
                'SYSTEMID~STORAGEID0~STORAGEID1'
            ],
            [
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1 SID=SYSTEMID~STORAGEID2 SID=SYSTEMID~',
                null,
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~',
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1 SID=SYSTEMID~STORAGEID2 SID=SYSTEMID~',
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1 SID=SYSTEMID~STORAGEID2 SID=SYSTEMID~'
            ],
            [
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1 SID=SYSTEMID~STORAGEID2 SID=SYSTEMID~',
                1,
                'SYSTEMID~STORAGEID0',
                'SID=SYSTEMID~STORAGEID0',
                'SID=SYSTEMID~STORAGEID0'
            ],
            [
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1 SID=SYSTEMID~STORAGEID2 SID=SYSTEMID~',
                50,
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~',
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1',
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1'
            ],
            [
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1 SID=SYSTEMID~STORAGEID2 SID=SYSTEMID~',
                35,
                'SYSTEMID~STORAGEID0~STORAGEID1',
                'SID=SYSTEMID~STORAGEID0',
                'SID=SYSTEMID~STORAGEID0'
            ],
            [
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~',
                null,
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~',
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1 SID=SYSTEMID~STORAGEID2 SID=SYSTEMID~',
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~'
            ],
            [
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~',
                1,
                'SYSTEMID~STORAGEID0',
                'SID=SYSTEMID~STORAGEID0',
                'SYSTEMID~STORAGEID0'
            ],
            [
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~',
                50,
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~',
                'SID=SYSTEMID~STORAGEID0 SID=SYSTEMID~STORAGEID1',
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~'
            ],
            [
                'SYSTEMID~STORAGEID0~STORAGEID1~STORAGEID2~',
                35,
                'SYSTEMID~STORAGEID0~STORAGEID1',
                'SID=SYSTEMID~STORAGEID0',
                'SYSTEMID~STORAGEID0~STORAGEID1'
            ]
        ];
    }
}
