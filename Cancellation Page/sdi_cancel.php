
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Evoqua SDI Contract Cancellation Tool</title>
 href="styles.css?v=20251002"
 
</head>
<body>
<div class="container">
  <h1>Evoqua SDI Contract Cancellation Tool</h1>

  <section id="cancellation-form">
    <h2>Generate Your Cancellation Letter</h2>
    <form id="generate-letter-form">
      <div class="form-grid">
        <!-- Form fields -->
        <div class="form-group">
          <label for="account-number">Account Number</label>
          <input type="text" id="account-number" required>
        </div>
        <div class="form-group">
          <label for="contract-number">Quote/Contract Number</label>
          <input type="text" id="contract-number" required>
        </div>
        <div class="form-group">
          <label for="contract-end-date">Contract End Date</label>
          <input type="date" id="contract-end-date" required>
        </div>
        <div class="form-group">
          <label for="termination-reason">Reason for Termination</label>
          <select id="termination-reason" required>
            <option value="">Select reason</option>
            <option value="end-of-term">End of Term</option>
            <option value="material-breach">Material Breach</option>
          </select>
        </div>
        <div class="form-group">
          <label for="your-name">Your Name</label>
          <input type="text" id="your-name" required>
        </div>
        <div class="form-group">
          <label for="your-position">Your Position/Company</label>
          <input type="text" id="your-position" required>
        </div>
        <div class="form-group">
          <label for="your-contact">Your Contact Information</label>
          <input type="text" id="your-contact" required>
        </div>
      </div>
      <button type="submit">Generate Letter</button>
    </form>
  </section>

  <section id="letter-output" class="hidden">
    <h2>Your Cancellation Letter</h2>
    <div id="generated-letter"></div>
    <br>
    <button class="btn-outline" id="download-word">Download as Word</button>
    <button class="btn-outline" id="download-pdf">Download as PDF</button>
  </section>
</div>


<script>
window.addEventListener('load', function () {
  const form = document.getElementById('generate-letter-form');
  const outputSection = document.getElementById('letter-output');
  const letterDiv = document.getElementById('generated-letter');
  const downloadWordBtn = document.getElementById('download-word');
  const downloadPdfBtn = document.getElementById('download-pdf');

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const accountNumber = document.getElementById('account-number').value.trim();
    const contractNumber = document.getElementById('contract-number').value.trim();
    const contractEndDate = document.getElementById('contract-end-date').value;
    const terminationReason = document.getElementById('termination-reason').value;
    const yourName = document.getElementById('your-name').value.trim();
    const yourPosition = document.getElementById('your-position').value.trim();
    const yourContact = document.getElementById('your-contact').value.trim();

    let noticeText = terminationReason === 'end-of-term'
      ? `We are providing this notice for end of term termination, in accordance with Section 10. The effective termination date will be ${contractEndDate}.`
      : `We are providing this notice due to material breach of contract, in accordance with Section 10. The effective termination date will be ${contractEndDate}.`;

    const letter = `
      <strong>Subject:</strong> Notice of Termination – SDI Service Agreement ${accountNumber} / ${contractNumber}<br><br>
      To: Evoqua Water Technologies Ltd.<br><br>
      Dear Evoqua Team,<br><br>
      ${noticeText}<br><br>
      We acknowledge our obligation to:<br>
      - Continue payment of the monthly rental service charge for the remainder of the term, if applicable.<br>
      - Promptly make all Evoqua-owned equipment available for removal upon expiration or termination.<br>
      - Settle all outstanding invoices and charges.<br><br>
      Please confirm receipt of this notice and provide instructions for equipment return and final settlement.<br><br>
      Thank you for your cooperation.<br><br>
      Sincerely,<br>
      ${yourName}<br>
      ${yourPosition}<br>
      ${yourContact}
    `;

    letterDiv.innerHTML = letter;
    outputSection.classList.remove('hidden');
    outputSection.scrollIntoView({ behavior: 'smooth' });
  });

  downloadWordBtn.addEventListener('click', function () {
    const blob = new Blob([letterDiv.innerHTML], { type: 'application/msword' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'Cancellation_Letter.doc';
    link.click();
  });

  downloadPdfBtn.addEventListener('click', function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.html(letterDiv, {
      callback: function (doc) {
        doc.save('Cancellation_Letter.pdf');
      },
      x: 10,
      y: 10
    });
  });
});
</script>
</body>
</html>
