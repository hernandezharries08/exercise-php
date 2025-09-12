<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculator Buttons</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="h-full p-4">
        <div class="grid grid-cols-4 gap-2 h-full">
            <!-- Row 1 -->
            <button onclick="inputNumber('7')" class="bg-gray-200 hover:bg-gray-300 rounded-md font-semibold">7</button>
            <button onclick="inputNumber('8')" class="bg-gray-200 hover:bg-gray-300 rounded-md font-semibold">8</button>
            <button onclick="inputNumber('9')" class="bg-gray-200 hover:bg-gray-300 rounded-md font-semibold">9</button>
            <button onclick="inputOperator('+')" class="bg-blue-500 hover:bg-blue-600 text-white rounded-md font-semibold">+</button>
            
            <!-- Row 2 -->
            <button onclick="inputNumber('4')" class="bg-gray-200 hover:bg-gray-300 rounded-md font-semibold">4</button>
            <button onclick="inputNumber('5')" class="bg-gray-200 hover:bg-gray-300 rounded-md font-semibold">5</button>
            <button onclick="inputNumber('6')" class="bg-gray-200 hover:bg-gray-300 rounded-md font-semibold">6</button>
            <button onclick="inputOperator('-')" class="bg-blue-500 hover:bg-blue-600 text-white rounded-md font-semibold">-</button>
            
            <!-- Row 3 -->
            <button onclick="inputNumber('1')" class="bg-gray-200 hover:bg-gray-300 rounded-md font-semibold">1</button>
            <button onclick="inputNumber('2')" class="bg-gray-200 hover:bg-gray-300 rounded-md font-semibold">2</button>
            <button onclick="inputNumber('3')" class="bg-gray-200 hover:bg-gray-300 rounded-md font-semibold">3</button>
            <button onclick="calculate()" class="bg-green-500 hover:bg-green-600 text-white rounded-md font-semibold">=</button>
            
            <!-- Row 4 -->
            <button onclick="inputNumber('0')" class="bg-gray-200 hover:bg-gray-300 rounded-md font-semibold col-span-2">0</button>
            <button onclick="clearAll()" class="bg-red-500 hover:bg-red-600 text-white rounded-md font-semibold">C</button>
            <button onclick="clearLast()" class="bg-orange-500 hover:bg-orange-600 text-white rounded-md font-semibold">⌫</button>
        </div>
    </div>

    <script>
        let expression = '';
        
        function inputNumber(num) {
            expression += num;
            sendToParent();
        }
        
        function inputOperator(op) {
            // only add operator if expression is not empty and doesn't end with operator
            if (expression && !isOperator(expression.slice(-1))) {
                expression += ' ' + op + ' ';
                sendToParent();
            }
        }
        
        function isOperator(char) {
            return char === '+' || char === '-';
        }
        
        function calculate() {
            try {
                // sanitize input for security
                const sanitizedExpression = expression.replace(/[^0-9+\-\s]/g, '');
                const result = eval(sanitizedExpression);
                
                // check if result is finite
                if (isFinite(result)) {
                    // send result to parent window
                    window.parent.postMessage({
                        type: 'calculator-result',
                        value: result.toString()
                    }, '*');
                    
                    // reset expression
                    expression = '';
                    sendToParent();
                } else {
                    alert('Invalid calculation');
                }
            } catch (error) {
                alert('Error in calculation');
                expression = '';
                sendToParent();
            }
        }
        
        function clearAll() {
            expression = '';
            sendToParent();
        }
        
        function clearLast() {
            if (expression.length > 0) {
                expression = expression.slice(0, -1);
                sendToParent();
            }
        }
        
        function sendToParent() {
            window.parent.postMessage({
                type: 'calculator-input',
                value: expression
            }, '*');
        }
    </script>
</body>
</html>
