<?php
$title = 'Add New Bus - Admin Panel';
$page_title = 'Add New Bus';
ob_start();
?>

<div class="bg-white rounded-xl border border-gray-100 p-6" style="max-width:720px;">
    <div style="margin-bottom:24px;">
        <a href="/DMS_BOOKING/admin/buses" style="color:#64748b;font-size:0.82rem;text-decoration:none;font-weight:500;">
            <i class="fas fa-arrow-left mr-1"></i>Back to Buses
        </a>
        <h2 style="font-size:1.25rem;font-weight:700;color:#0f172a;margin-top:10px;">
            <i class="fas fa-plus-circle text-blue-500 mr-2"></i>Add New Bus
        </h2>
    </div>

    <form method="POST" action="/DMS_BOOKING/admin/buses" style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
        <!-- Bus Number -->
        <div style="grid-column:span 2;">
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Bus Number <span style="color:#dc2626;">*</span></label>
            <input type="text" name="bus_number" required placeholder="e.g. DMS-001"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <!-- From -->
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">From Location <span style="color:#dc2626;">*</span></label>
            <input type="text" name="from_location" required placeholder="e.g. Davao City"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <!-- To -->
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">To Location <span style="color:#dc2626;">*</span></label>
            <input type="text" name="to_location" required placeholder="e.g. General Santos"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <!-- Journey Date -->
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Journey Date <span style="color:#dc2626;">*</span></label>
            <input type="date" name="journey_date" required min="<?php echo date('Y-m-d'); ?>"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <!-- Journey Time -->
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Departure Time</label>
            <input type="time" name="journey_time"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <!-- Total Seats -->
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Total Seats <span style="color:#dc2626;">*</span></label>
            <input type="number" name="total_seats" required min="1" max="100" placeholder="e.g. 45"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'"
                   oninput="this.form.available_seats.max=this.value;">
        </div>

        <!-- Available Seats -->
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Available Seats <span style="color:#dc2626;">*</span></label>
            <input type="number" name="available_seats" required min="0" placeholder="e.g. 45"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <!-- Price per Seat -->
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Price per Seat (₱) <span style="color:#dc2626;">*</span></label>
            <input type="number" name="price_per_seat" required min="1" step="0.01" placeholder="e.g. 350.00"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <!-- Bus Type -->
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Bus Type</label>
            <select name="bus_type" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;background:#fff;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
                <option value="standard">Standard</option>
                <option value="ac">AC (Air-Conditioned)</option>
                <option value="sleeper">Sleeper</option>
            </select>
        </div>

        <!-- Buttons -->
        <div style="grid-column:span 2;display:flex;gap:10px;margin-top:6px;">
            <button type="submit" style="padding:11px 28px;background:linear-gradient(135deg,#1d4ed8,#2563eb);color:#fff;border:none;border-radius:8px;font-size:0.875rem;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(37,99,235,0.3);">
                <i class="fas fa-save mr-2"></i>Save Bus
            </button>
            <a href="/DMS_BOOKING/admin/buses" style="padding:11px 22px;background:#f1f5f9;color:#475569;border-radius:8px;font-size:0.875rem;font-weight:600;text-decoration:none;">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../admin/layouts/app.blade.php';
?>
