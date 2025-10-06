<style>
    main {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        padding: 20px;
        max-width: 80vw;
        max-height: 88vh !important;
        overflow: auto !important;
    }

    .main-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin: 0 auto;
        min-width: 2500px !important;
        /* overflow: auto !important; */
    }

    .scroll-container {
        width: 100%;
        /* overflow-x: auto; */
        border: 1px solid #dee2e6;
        border-radius: 4px;
        margin-top: 20px;
    }

    .form-table {
        min-width: 2500px;
        /* Increased to ensure scrolling */
        width: 100%;
    }

    .form-table>div {
        display: flex;
        border-bottom: 1px solid #dee2e6;
    }

    .form-table>div>div {
        padding: 8px;
        border-right: 1px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .form-table>div>div:last-child {
        border-right: none;
    }

    .header-row {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    input {
        border: 1px solid #ced4da;
        padding: 4px 8px;
        border-radius: 4px;
        width: 100%;
        box-sizing: border-box;
    }

    .nested-columns {
        display: flex;
        flex-direction: column;
    }

    .nested-row {
        display: flex;
        flex: 1;
    }

    .nested-cell {
        flex: 1;
        border-right: 1px solid #dee2e6;
        padding: 4px;
        text-align: center;
        font-size: 0.85rem;
    }

    .nested-cell:last-child {
        border-right: none;
    }

    .scroll-indicator {
        text-align: center;
        color: #6c757d;
        font-size: 0.9rem;
        margin-top: 5px;
    }

    .form-title {
        border-bottom: 2px solid #0d6efd;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .form-section {
        margin-bottom: 15px;
    }
</style>
 <main>
    <?php
        $stmt = $pdo->prepare("SELECT * FROM sf_add_data");
        $stmt->execute();
        $data_sf_four = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
    <form class="main-container" id="sfFour-form">
        <input type="hidden" name="id" value="<?= htmlspecialchars($data_sf_four["sf_add_data_id"] ?? '') ?>">
        <div class="col-md-12 d-flex justify-content-between">
            <div class="col-md-3 d-flex align-items-center justify-content-start">
                 <img src="../../assets/image/logo.png" alt="No Image" style="width: auto; height: 150px;">
            </div>
            <div class="col-md-6">
                   <div class="form-title text-center w-100">
                        <h2>School Form 1 (SF1) School Register</h2>
                        <p class="text-muted">(this replaces Form 1, Master List & STS Form 2-Family Background and Profile)</p>
                    </div>
            </div>
            <div class="col-md-3 d-flex align-items-center justify-content-end ">
                <img src="../../assets/image/deped.png" alt="No Image" style="width: 200px; height: auto; transform: translateX(-30px);">
            </div>
        </div>
      

         <div class="form-section">
             <div class="row mb-2">
                 <div class="col-md-4">
                     <div class="d-flex align-items-center mb-2">
                         <label class="me-2 col-4">School ID</label>
                         <input type="text" name="school_id" value="<?= htmlspecialchars($data_sf_four["school_id"] ?? '') ?>" class="me-2 flex-grow-1">
                         <input type="text" name="region" value="<?= htmlspecialchars($data_sf_four["region"] ?? '') ?>" class="flex-grow-1" placeholder="Region">
                     </div>
                 </div>
                 <div class="col-md-4">
                     <div class="d-flex align-items-center mb-2">
                         <label class="me-2 col-4">Division</label>
                         <input type="text" name="Division" value="<?= htmlspecialchars($data_sf_four["Division"] ?? '') ?>" class="flex-grow-1">
                     </div>
                 </div>
                 <div class="col-md-4">
                     <div class="d-flex align-items-center mb-2">
                         <label class="me-2 col-4">District</label>
                         <input type="text" name="district" value="<?= htmlspecialchars($data_sf_four["district"] ?? '') ?>" class="flex-grow-1">
                     </div>
                 </div>
             </div>

             <div class="row mb-3">
                 <div class="col-md-4">
                     <div class="d-flex align-items-center mb-2">
                         <label class="me-2 col-4">School Name</label>
                         <input type="text" name="school_name" value="<?= htmlspecialchars($data_sf_four["school_name"] ?? '') ?>" class="flex-grow-1">
                     </div>
                 </div>
                 <div class="col-md-4">
                     <div class="d-flex align-items-center mb-2">
                         <label class="me-2 col-4">School Year</label>
                            <?php
                                $stmt = $pdo->prepare("SELECT * FROM school_year WHERE school_year_status = 'Active'");
                                $stmt->execute();
                                $sy = $stmt->fetch(PDO::FETCH_ASSOC);
                            ?>
                           <input readonly class="form-control" type="text" name="school_year_name" value="<?= $sy["school_year_name"] ?>">
                     </div>
                 </div>
                 <div class="col-md-4">
                     <div class="d-flex align-items-center mb-2">
                            <label class="me-2 col-2">Grade Level</label>
                            <input type="text" name="school_id" value="<?= htmlspecialchars($data_sf_four["school_id"] ?? '') ?>" class="me-2 flex-grow-1">
                            <label class="me-2 col-1">Section</label>
                            <input type="text" name="region" value="<?= htmlspecialchars($data_sf_four["region"] ?? '') ?>" class="flex-grow-1" placeholder="Region">
                     </div>
                 </div>
             </div>
         </div>
    </form>

 </main>
 <script>
document.addEventListener('DOMContentLoaded', function() {
    // Get the Generate Report button
    const generateReportBtn = document.querySelector('.btn-secondary');
    
    // Add click event listener
    generateReportBtn.addEventListener('click', function(e) {
        e.preventDefault();
        generatePrintableReport();
    });
    
    function generatePrintableReport() {
        // Store original content
        const originalContent = document.querySelector('.main-container').innerHTML;
        
        // Create a print-friendly version
        const printContent = createPrintFriendlyContent();
        
        // Open print window
        const printWindow = window.open('', '_blank', 'width=1000,height=600');
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>School Form 4 (SF4) Monthly Learner's Movement and Attendance</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 20px;
                        color: #000;
                    }
                    .print-container {
                        width: 100%;
                    }
                    .form-title {
                        text-align: center;
                        border-bottom: 2px solid #0d6efd;
                        padding-bottom: 10px;
                        margin-bottom: 20px;
                    }
                    .form-title h2 {
                        margin: 0;
                        font-size: 18px;
                    }
                    .form-title p {
                        margin: 5px 0 0 0;
                        font-size: 12px;
                        color: #666;
                    }
                    .school-info {
                        margin-bottom: 20px;
                        font-size: 12px;
                    }
                    .school-info table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    .school-info td {
                        padding: 2px 5px;
                        vertical-align: top;
                    }
                    table.data-table {
                        width: 100%;
                        border-collapse: collapse;
                        font-size: 10px;
                        margin-top: 10px;
                    }
                    table.data-table th,
                    table.data-table td {
                        border: 1px solid #000;
                        padding: 4px;
                        text-align: center;
                        vertical-align: middle;
                    }
                    table.data-table th {
                        background-color: #f8f9fa !important;
                        font-weight: bold;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    .section-header {
                        background-color: #e9ecef !important;
                        font-weight: bold;
                        text-align: left;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    .mortality-section {
                        margin-top: 20px;
                        font-size: 12px;
                    }
                    .mortality-section table {
                        width: auto;
                        border-collapse: collapse;
                    }
                    .mortality-section th,
                    .mortality-section td {
                        border: 1px solid #000;
                        padding: 4px 8px;
                        text-align: left;
                    }
                    @media print {
                        body { margin: 0; }
                        .print-container { width: 100%; }
                        table.data-table { font-size: 9px; }
                    }
                    @page {
                        size: landscape;
                        margin: 10mm;
                    }
                </style>
            </head>
            <body>
                ${printContent}
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(function() {
                            window.close();
                        }, 500);
                    };
                <\/script>
            </body>
            </html>
        `);
        
        printWindow.document.close();
    }
    
    function createPrintFriendlyContent() {
        // Clone the main container
        const mainContainer = document.querySelector('.main-container').cloneNode(true);
        
        // Remove form elements and buttons
        const form = mainContainer.querySelector('form');
        if (form) {
            form.removeAttribute('id');
            form.removeAttribute('class');
        }
        
        // Remove input fields and replace with display values
        const inputs = mainContainer.querySelectorAll('input');
        inputs.forEach(input => {
            const span = document.createElement('span');
            span.textContent = input.value;
            span.style.padding = '0 5px';
            input.parentNode.replaceChild(span, input);
        });
        
        // Remove the save and generate buttons
        const buttons = mainContainer.querySelectorAll('button');
        buttons.forEach(button => button.remove());
        
        // Get school information for the header
        const schoolInfo = `
            <div class="school-info">
                <table>
                    <tr>
                        <td style="width: 30%;"><strong>School ID:</strong> ${document.querySelector('input[name="school_id"]')?.value || ''}</td>
                        <td style="width: 30%;"><strong>Region:</strong> ${document.querySelector('input[name="region"]')?.value || ''}</td>
                        <td style="width: 40%;"><strong>Division:</strong> ${document.querySelector('input[name="Division"]')?.value || ''}</td>
                    </tr>
                    <tr>
                        <td><strong>School Name:</strong> ${document.querySelector('input[name="school_name"]')?.value || ''}</td>
                        <td><strong>School Year:</strong> ${document.querySelector('input[name="school_year_name"]')?.value || ''}</td>
                        <td><strong>Report Month:</strong> ${formatDate(document.querySelector('input[name="report_for_the_month_of"]')?.value || '')}</td>
                    </tr>
                </table>
            </div>
        `;
        
        return `
            <div class="print-container">
                <div class="form-title">
                    <h2>School Form 4 (SF4) Monthly Learner's Movement and Attendance</h2>
                    <p>(this replaces Form 3 & STS Form 4-Absenteeism and Dropout Profile)</p>
                </div>
                ${schoolInfo}
                ${mainContainer.querySelector('.scroll-container').outerHTML}
                <div class="mortality-section">
                    <strong>Mortality Death</strong>
                    <table style="margin-top: 10px;">
                        <thead>
                            <tr>
                                <th>Previous Month</th>
                                <th>${document.querySelector('input[name="Previous_Month"]')?.value || ''}</th>
                                <th>For the month</th>
                                <th>${document.querySelector('input[name="For_the_month"]')?.value || ''}</th>
                                <th>Cumulative as of End of Month</th>
                                <th>${document.querySelector('input[name="Cumulative_as_of_End_of_Month"]')?.value || ''}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div style="margin-top: 20px; font-size: 11px; text-align: center;">
                    <p>Generated on: ${new Date().toLocaleDateString()} ${new Date().toLocaleTimeString()}</p>
                </div>
            </div>
        `;
    }
    
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long' 
        });
    }
});
</script>