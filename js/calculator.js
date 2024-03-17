let button = document.querySelector('.block-button')
const header = document.getElementById("page-header")
let calcButton;
let block_url = 'http://localhost:8080/moodle/blocks/calculator'

button.addEventListener('click', () => {
    if ( !document.querySelector('.calculate-block') ){
        modal = document.createElement('div')    
        history.php
        modal.innerHTML += `
            <div class="calculate-block">
                <div class="title">Решение квадратного уравнения</div>
                <div class="fields">
                    <div class="field"> a  <input name="a" placeholder="введите значение"></div>
                    <div class="field"> b  <input name="b" placeholder="введите значение"></div>
                    <div class="field"> c  <input name="c" placeholder="введите значение"></div>
                </div>

                <button class="modal-calc-button">Найти решение</button>
                <a class="history" href="${block_url}/history.php">История</a>
            </div>
        `    
        header.parentNode.insertBefore(modal, header)
        calcButton = document.querySelector('.modal-calc-button')

        calcButton.addEventListener('click', () => {
            if (checkInput()){
                var a = parseFloat(document.querySelector('input[name="a"]').value);
                var b = parseFloat(document.querySelector('input[name="b"]').value);
                var c = parseFloat(document.querySelector('input[name="c"]').value);
                let result = solveQuadratic(a, b, c)
                let stringResult = showResult(result)
                sendResult(a,b,c,stringResult)

            }
        });
    } else {
        document.querySelector('.calculate-block').remove()
    }
})

function solveQuadratic(a, b, c) {
    var discriminant = b * b - 4 * a * c;
    if (discriminant > 0) {
        var root1 = (-b + Math.sqrt(discriminant)) / (2 * a);
        var root2 = (-b - Math.sqrt(discriminant)) / (2 * a);
        return [root1, root2];
    } else if (discriminant === 0) {
        var root = -b / (2 * a);
        return [root];

    } else {
        var realPart = -b / (2 * a);
        var imaginaryPart = Math.sqrt(-discriminant) / (2 * a);
        return [realPart, imaginaryPart, realPart, -imaginaryPart];
    }
}

function checkInput(){
    let errors = document.querySelectorAll('.text-error'); 
    if (errors.length !== 0){
        errors.forEach((i) => i.remove());
    }
    const inputs = document.querySelectorAll('.fields .field input');
    let hasError = false;
    
    inputs.forEach(element => {
        if ( isNaN(parseFloat(element.value)) ){
            let error = document.createElement('div')
            error.classList.add('text-error')
            error.innerText = "invalid value"
            element.parentNode.insertBefore(error, element.nextSibling);
            hasError = true
        }
    })

    return !hasError
}


function showResult(result){
    let oldResult = document.querySelector('.result');
    if ( oldResult ){
        oldResult.remove()
    }
    let resultElement = document.createElement('div')
    resultElement.classList.add('result')
    let button = document.querySelector('.modal-calc-button')
    let resultString;
    if (result.length === 1){
        resultString = `Root: ${result[0]}`
        resultElement.innerText = resultString
        button.parentElement.insertBefore(resultElement, button)
    } else if (result.length === 2) {
        resultString = `Root 1: ${result[0]}\nRoot 2: ${result[1]}`
        resultElement.innerText = resultString
        button.parentElement.insertBefore(resultElement, button)

    } else if (result.length === 4) {
        resultString = `Complex root 1: ${result[0]}+${result[1]}i\n Complex root 2: ${result[2]} ${result[3]}i`
        resultElement.innerText = resultString
        button.parentElement.insertBefore(resultElement, button)
    }
    return resultString
}

function sendResult(a, b, c, result){
    const url = `${block_url}/ajax.php`
    $.ajax({
        url: url,
        data: {
            'a': a,
            'b': b,
            'c': c,
            'result': result.replace('\n', '\ and\ ')
        },
        type: 'POST',
    })
}