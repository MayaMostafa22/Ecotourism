<?php
require_once __DIR__ . '/../models/BookingEscrow.php';
require_once __DIR__ . '/../models/Booking.php';

class BookingEscrowController extends Controller
{
    private $escrowModel;
    private $bookingModel;

    public function __construct()
    {
        $this->escrowModel  = new BookingEscrow();
        $this->bookingModel = new Booking();
    }

    // READ ALL
    public function index()
    {
        $escrows = $this->escrowModel->getAllEscrows();
        $this->view("escrow/index", ['escrows' => $escrows]);
    }

    // SHOW ONE
    public function show($id)
    {
        $escrow = $this->escrowModel->getEscrowById($id);
        $this->view("escrow/show", ['escrow' => $escrow]);
    }

    // SHOW CREATE FORM
    public function create()
    {
        $this->view("escrow/create");
    }

    // STORE - "hold traveler payment in escrow"
    public function store()
    {
        $bookingId     = $_POST['bookingId'];
        $amountHeld    = $_POST['amountHeld'];
        $entryDate     = $_POST['entryDate'];
        $releasedDate  = $_POST['releasedDate'];

        $status = "Held";

        $result = $this->escrowModel->createEscrow(
            $bookingId,
            $status,
            $amountHeld,
            $entryDate,
            $releasedDate
        );

        if ($result) {
           // header("Location: " . BASE_URL . "BookingEscrow/index");
        } else {
            $this->view("escrow/create", ['error' => 'Error holding payment']);
        }
    }

    // RELEASE ESCROW
    public function release($id)
    {
        $escrow  = $this->escrowModel->getEscrowById($id);
        $booking = $this->bookingModel->getBookingById($escrow['BookingId']);

        if ($booking['Status'] == 'Confirmed') {
            $this->escrowModel->updateEscrow(
                $id,
                'Released',
                $escrow['AmountHeld'],
                date('Y-m-d H:i:s') 
            );
          
        } else {
            $this->view("escrow/show", [
                'escrow' => $escrow,
                'error'  => 'Experience not completed yet'
            ]);
        }
    }

    // REFUND ESCROW 
    public function refund($id)
    {
        $escrow  = $this->escrowModel->getEscrowById($id);
        $booking = $this->bookingModel->getBookingById($escrow['BookingId']);

        if ($booking['Status'] == 'Cancelled') {
            $this->escrowModel->updateEscrow(
                $id,
                'Refunded',
                $escrow['AmountHeld'],
                date('Y-m-d H:i:s')
            );
           // header("Location: " . BASE_URL . "BookingEscrow/index");
        } else {
            $this->view("escrow/show", [
                'escrow' => $escrow,
                'error'  => 'Booking is not cancelled'
            ]);
        }
    }

    // DELETE
    public function delete($id)
    {
        $this->escrowModel->deleteEscrow($id);
    }
}