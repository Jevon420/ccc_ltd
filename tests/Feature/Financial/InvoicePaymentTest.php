<?php

namespace Tests\Feature\Financial;

use App\Livewire\Invoices\InvoiceForm;
use App\Livewire\Invoices\InvoiceList;
use App\Livewire\Invoices\InvoiceShow;
use App\Livewire\Payments\PaymentList;
use App\Livewire\Payments\RecordPayment;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvoicePaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $financeOfficer;

    protected User $driver;

    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->financeOfficer = User::factory()->create();
        $this->financeOfficer->assignRole('Finance Officer');

        $this->driver = User::factory()->create();
        $this->driver->assignRole('Driver');

        $this->client = Client::factory()->create();
    }

    public function test_invoices_index_loads(): void
    {
        $this->actingAs($this->financeOfficer)
            ->get(route('dashboard.invoices.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(InvoiceList::class);
    }

    public function test_invoice_gets_auto_reference_and_calculates_tax(): void
    {
        $invoice = Invoice::create([
            'client_id' => $this->client->id,
            'title' => 'Test Invoice',
            'subtotal' => 1000,
            'tax_rate' => 12.5,
            'status' => 'draft',
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
        ]);

        $this->assertStringStartsWith('INV-', $invoice->reference);
        $this->assertEquals(125.00, (float) $invoice->tax_amount);
        $this->assertEquals(1125.00, (float) $invoice->total);
    }

    public function test_create_invoice(): void
    {
        Livewire::actingAs($this->financeOfficer)
            ->test(InvoiceForm::class)
            ->call('openCreate')
            ->set('title', 'Monthly Cleaning — June 2026')
            ->set('clientId', $this->client->id)
            ->set('subtotal', '2500')
            ->set('taxRate', '0')
            ->set('issueDate', '2026-06-01')
            ->set('dueDate', '2026-06-30')
            ->call('save')
            ->assertSet('isOpen', false);

        $this->assertDatabaseHas('invoices', [
            'title' => 'Monthly Cleaning — June 2026',
            'client_id' => $this->client->id,
            'subtotal' => '2500.00',
        ]);
    }

    public function test_mark_invoice_sent(): void
    {
        $invoice = Invoice::factory()->draft()->create(['client_id' => $this->client->id]);

        Livewire::actingAs($this->financeOfficer)
            ->test(InvoiceShow::class, ['invoice' => $invoice])
            ->call('markSent');

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'sent']);
    }

    public function test_record_payment_updates_invoice_to_paid(): void
    {
        $invoice = Invoice::factory()->sent()->create([
            'client_id' => $this->client->id,
            'subtotal' => 1000,
            'total' => 1000,
        ]);

        Livewire::actingAs($this->financeOfficer)
            ->test(RecordPayment::class)
            ->call('open', $invoice->id)
            ->set('amount', '1000')
            ->set('method', 'bank_transfer')
            ->set('paidAt', now()->format('Y-m-d'))
            ->call('save');

        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoice->id,
            'amount' => '1000.00',
            'status' => 'confirmed',
        ]);

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'paid']);
    }

    public function test_partial_payment_sets_partial_status(): void
    {
        $invoice = Invoice::factory()->sent()->create([
            'client_id' => $this->client->id,
            'subtotal' => 2000,
            'total' => 2000,
        ]);

        Livewire::actingAs($this->financeOfficer)
            ->test(RecordPayment::class)
            ->call('open', $invoice->id)
            ->set('amount', '500')
            ->set('method', 'cash')
            ->set('paidAt', now()->format('Y-m-d'))
            ->call('save');

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'partial']);
        $this->assertEquals(500, (float) $invoice->fresh()->amount_paid);
    }

    public function test_payments_page_loads(): void
    {
        $this->actingAs($this->financeOfficer)
            ->get(route('dashboard.payments.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(PaymentList::class);
    }

    public function test_driver_cannot_view_invoices(): void
    {
        $this->actingAs($this->driver)
            ->get(route('dashboard.invoices.index'))
            ->assertStatus(403);
    }

    public function test_balance_due_calculated_correctly(): void
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'subtotal' => 3000,
            'total' => 3000,
            'amount_paid' => 1000,
        ]);

        $this->assertEquals(2000.00, $invoice->balanceDue);
    }
}
