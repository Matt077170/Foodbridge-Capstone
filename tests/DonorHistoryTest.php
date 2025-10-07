<?php
use PHPUnit\Framework\TestCase;
use App\DonorHistory;


class DonorHistoryTest extends TestCase {
    private $mockConn;
    private $donationHistory;

    protected function setUp(): void {
        $this->mockConn = $this->createMock(mysqli::class);
        $this->donationHistory = new DonorHistory($this->mockConn);
    }

    public function testGetDonorHistoryReturnsRecentDonations() {
        $expected = [
            [
                'created_at' => '2025-09-19 14:00:00',
                'caption' => 'Food Pack',
                'delivery_mode' => 'Drop-off',
                'same_day_delivery' => 1,
                'status' => 'completed'
            ],
            [
                'created_at' => '2025-09-18 10:30:00',
                'caption' => 'Clothing',
                'delivery_mode' => 'Pickup',
                'same_day_delivery' => 0,
                'status' => 'pending'
            ]
        ];

        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockResult = $this->createMock(mysqli_result::class);

        $mockResult->method('fetch_all')->willReturn($expected);
        $mockStmt->method('get_result')->willReturn($mockResult);
        $this->mockConn->method('prepare')->willReturn($mockStmt);

        $result = $this->donationHistory->getDonorHistory(1);
        $this->assertEquals($expected, $result);
    }
}
