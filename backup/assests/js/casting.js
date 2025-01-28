function selectOption(value) {
    const nextBtn = document.getElementById('next-btn');
    const options = document.querySelectorAll('input[name="option"]');
    options.forEach(option => {
        if (option.value === value) {
            option.checked = true;
        } else {
            option.checked = false;
        }
    });

    const optionDivs = document.querySelectorAll('.option');
    optionDivs.forEach(optionDiv => {
        optionDiv.classList.remove('selected');
    });

    const selectedOptionDiv = document.querySelector(`input[value="${value}"]`).closest('.option');
    selectedOptionDiv.classList.add('selected');
    nextBtn.disabled = false;
    nextBtn.classList.remove('disabled');
    nextBtn.classList.add('btn-secondary');
}