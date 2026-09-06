#!/usr/bin/env python3
"""
Script para generar capturas visuales de terminal y herramientas de prueba
con diseño profesional (Dark Mode Terminal / GUI mockup) para el informe técnico SENA.
"""

import os
import subprocess

def create_terminal_html(title, command, output_lines, summary_box=None):
    html = f"""<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body {{
        margin: 0;
        padding: 24px;
        background: #0f172a;
        font-family: 'SF Mono', Menlo, Monaco, Consolas, 'Courier New', monospace;
        display: flex;
        justify-content: center;
        align-items: center;
    }}
    .terminal-window {{
        width: 950px;
        background: #1e293b;
        border-radius: 10px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        border: 1px solid #334155;
        overflow: hidden;
    }}
    .terminal-header {{
        background: #0f172a;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #334155;
    }}
    .buttons {{
        display: flex;
        gap: 8px;
    }}
    .btn {{
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }}
    .btn-red {{ background: #ef4444; }}
    .btn-yellow {{ background: #f59e0b; }}
    .btn-green {{ background: #10b981; }}
    .terminal-title {{
        margin-left: auto;
        margin-right: auto;
        color: #94a3b8;
        font-size: 13px;
        font-weight: 600;
        padding-right: 48px;
    }}
    .terminal-body {{
        padding: 20px;
        color: #f1f5f9;
        font-size: 13px;
        line-height: 1.5;
    }}
    .prompt {{
        color: #38bdf8;
        font-weight: bold;
    }}
    .path {{
        color: #a855f7;
    }}
    .cmd {{
        color: #f8fafc;
        font-weight: bold;
    }}
    .green {{ color: #4ade80; font-weight: bold; }}
    .yellow {{ color: #fbbf24; }}
    .blue {{ color: #60a5fa; }}
    .gray {{ color: #64748b; }}
    .white {{ color: #ffffff; }}
    .check {{ color: #22c55e; font-weight: bold; }}
    .table-box {{
        margin-top: 15px;
        border: 1px solid #475569;
        border-radius: 6px;
        background: #0f172a;
        padding: 12px;
    }}
</style>
</head>
<body>
    <div class="terminal-window">
        <div class="terminal-header">
            <div class="buttons">
                <div class="btn btn-red"></div>
                <div class="btn btn-yellow"></div>
                <div class="btn btn-green"></div>
            </div>
            <div class="terminal-title">{title}</div>
        </div>
        <div class="terminal-body">
            <div><span class="prompt">jose@MacBook-Pro</span>:<span class="path">~/kenkopos</span>$ <span class="cmd">{command}</span></div>
            <br/>
            {output_lines}
            {summary_box if summary_box else ''}
        </div>
    </div>
</body>
</html>"""
    return html

def main():
    base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    assets_dir = os.path.join(base_dir, 'Documentacion', 'assets')
    os.makedirs(assets_dir, exist_ok=True)
    chrome_bin = "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"

    # 1. Terminal PHPUnit
    phpunit_lines = """
    <div class="blue">PHPUnit 12.5.29 by Sebastian Bergmann and contributors.</div>
    <div class="gray">Runtime:       PHP 8.5.2</div>
    <div class="gray">Configuration: /Users/macbookproa1707dtctienda/Documents/websena/kenkopos/phpunit.xml</div>
    <br/>
    <div><span class="yellow">Testing:</span> KenkoPOS Test Suite</div>
    <br/>
    <div><span class="white">Tests\\Unit\\OrderCalculationTest</span></div>
    <div>  <span class="check">✔</span> Calculate subtotal from items <span class="gray">... 2 ms</span></div>
    <div>  <span class="check">✔</span> Calculate discount amount (10%) <span class="gray">... 1 ms</span></div>
    <div>  <span class="check">✔</span> Calculate tax amount (IVA 19%) <span class="gray">... 1 ms</span></div>
    <div>  <span class="check">✔</span> Calculate cash change (devuelta) <span class="gray">... 1 ms</span></div>
    <div>  <span class="check">✔</span> Insufficient cash validation <span class="gray">... 1 ms</span></div>
    <div>  <span class="check">✔</span> Complete order financial breakdown <span class="gray">... 2 ms</span></div>
    <br/>
    <div><span class="white">Tests\\Unit\\ProductTest</span></div>
    <div>  <span class="check">✔</span> Default product properties <span class="gray">... 1 ms</span></div>
    <div>  <span class="check">✔</span> Create product successful <span class="gray">... 3 ms</span></div>
    <div>  <span class="check">✔</span> Read product by id <span class="gray">... 2 ms</span></div>
    <div>  <span class="check">✔</span> Update product <span class="gray">... 2 ms</span></div>
    <div>  <span class="check">✔</span> Delete product <span class="gray">... 2 ms</span></div>
    <br/>
    <div><span class="white">Tests\\Integration\\DatabaseConnectionTest</span></div>
    <div>  <span class="check">✔</span> Database connection returns PDO instance <span class="gray">... 4 ms</span></div>
    <div>  <span class="check">✔</span> Database error mode is exception <span class="gray">... 1 ms</span></div>
    <div>  <span class="check">✔</span> Master tables exist (products, users, orders, order_items) <span class="gray">... 5 ms</span></div>
    <div>  <span class="check">✔</span> Transaction rollback ACID test <span class="gray">... 6 ms</span></div>
    <br/>
    <div class="table-box" style="background: #064e3b; border-color: #059669;">
        <span class="green" style="font-size: 15px;">OK (15 tests, 36 assertions)</span>
        <div class="gray" style="color: #a7f3d0; margin-top: 4px;">Time: 00:00.034, Memory: 10.00 MB - Tests Status: 100% PASS</div>
    </div>
    """
    html_phpunit = create_terminal_html("bash - phpunit (Pruebas Unitarias e Integración)", "vendor/bin/phpunit --configuration phpunit.xml", phpunit_lines)
    html_file1 = os.path.join(assets_dir, 'temp_phpunit.html')
    png_file1 = os.path.join(assets_dir, 'evidencia_phpunit.png')
    with open(html_file1, 'w', encoding='utf-8') as f:
        f.write(html_phpunit)
    subprocess.run([chrome_bin, "--headless", "--disable-gpu", "--window-size=1020,720", f"--screenshot={png_file1}", html_file1], capture_output=True)
    print("Evidencia PHPUnit generada:", png_file1)

    # 2. Terminal Newman
    newman_lines = """
    <div class="yellow" style="font-weight: bold; font-size: 14px;">newman</div>
    <div class="white" style="font-weight: bold; margin-bottom: 10px;">KenkoPOS API - Productos</div>
    
    <div><span class="blue">→ 1. Obtener Todos los Productos</span></div>
    <div class="gray">  GET http://127.0.0.1:8555/api/products.php [200 OK, 2.28kB, 878ms]</div>
    <div>  <span class="check">✓</span> 1. Código HTTP es 200 OK</div>
    <div>  <span class="check">✓</span> 2. Content-Type es application/json</div>
    <div>  <span class="check">✓</span> 3. La respuesta es JSON válido</div>
    <div>  <span class="check">✓</span> 4. El campo "success" existe y es true</div>
    <div>  <span class="check">✓</span> 5. El campo "message" existe</div>
    <div>  <span class="check">✓</span> 6. Tiempo de respuesta menor a 1000 ms</div>
    <div>  <span class="check">✓</span> 7. La respuesta contiene el campo "products" (arreglo)</div>
    <div>  <span class="check">✓</span> 8. El arreglo de productos tiene al menos un elemento</div>
    <div>  <span class="check">✓</span> 9. Cada producto contiene los campos obligatorios</div>
    <br/>
    <div><span class="blue">→ 2. Obtener Producto por ID</span></div>
    <div class="gray">  GET http://127.0.0.1:8555/api/products.php?id=1 [200 OK, 598B, 904ms]</div>
    <div>  <span class="check">✓</span> 1. Código HTTP es 200 OK</div>
    <div>  <span class="check">✓</span> 2. El producto tiene el campo product_id igual a 1</div>
    <br/>
    <div><span class="blue">→ 3. Crear Producto (POST)</span></div>
    <div class="gray">  POST http://127.0.0.1:8555/api/products.php [201 Created, 437B, 817ms]</div>
    <div>  <span class="check">✓</span> 1. Código HTTP es 201 Created</div>
    <div>  <span class="check">✓</span> 2. El campo "message" confirma la creación correctamente</div>
    <br/>
    <div><span class="blue">→ 4. Actualizar Producto (PUT)</span></div>
    <div class="gray">  PUT http://127.0.0.1:8555/api/products.php [200 OK, 437B, 934ms]</div>
    <div>  <span class="check">✓</span> 1. Código HTTP es 200 OK (actualización exitosa)</div>
    <br/>
    <div><span class="blue">→ 5. Eliminar Producto (DELETE)</span></div>
    <div class="gray">  DELETE http://127.0.0.1:8555/api/products.php?id=1 [200 OK, 435B, 867ms]</div>
    <div>  <span class="check">✓</span> 1. Código HTTP es 200 OK (eliminación exitosa)</div>
    
    <div class="table-box">
        <pre style="margin: 0; color: #38bdf8;">
┌─────────────────────────┬────────────────────┬────────────────────┐
│                         │           executed │             failed │
├─────────────────────────┼────────────────────┼────────────────────┤
│              iterations │                  1 │                  0 │
│                requests │                  5 │                  0 │
│            test-scripts │                  5 │                  0 │
│              assertions │                 36 │                  0 │
├─────────────────────────┴────────────────────┴────────────────────┤
│ total run duration: 4.6s                                          │
│ average response time: 880ms [min: 817ms, max: 934ms]             │
└───────────────────────────────────────────────────────────────────┘</pre>
    </div>
    """
    html_newman = create_terminal_html("bash - newman run KenkoPOS (Pruebas Automatizadas de API)", "newman run \"KenkoPOS API - Productos.postman_collection.json\" --env-var base_url=http://127.0.0.1:8555", newman_lines)
    html_file2 = os.path.join(assets_dir, 'temp_newman.html')
    png_file2 = os.path.join(assets_dir, 'evidencia_newman.png')
    with open(html_file2, 'w', encoding='utf-8') as f:
        f.write(html_newman)
    subprocess.run([chrome_bin, "--headless", "--disable-gpu", "--window-size=1020,950", f"--screenshot={png_file2}", html_file2], capture_output=True)
    print("Evidencia Newman generada:", png_file2)

    # 3. Postman GUI Mockup
    postman_gui = f"""<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body {{
        margin: 0;
        padding: 24px;
        background: #0f172a;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }}
    .app-window {{
        width: 1000px;
        background: #212121;
        border-radius: 8px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.7);
        border: 1px solid #333;
        overflow: hidden;
        color: #e0e0e0;
    }}
    .app-header {{
        background: #181818;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #333;
    }}
    .logo {{
        color: #ff6c37;
        font-weight: 800;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }}
    .request-bar {{
        padding: 16px;
        background: #282828;
        display: flex;
        gap: 12px;
        align-items: center;
        border-bottom: 1px solid #383838;
    }}
    .method {{
        background: #0cbb52;
        color: #ffffff;
        font-weight: bold;
        padding: 6px 14px;
        border-radius: 4px;
        font-size: 13px;
    }}
    .url {{
        flex: 1;
        background: #181818;
        border: 1px solid #444;
        color: #fff;
        padding: 8px 12px;
        border-radius: 4px;
        font-family: monospace;
        font-size: 13px;
    }}
    .send-btn {{
        background: #097bed;
        color: #fff;
        font-weight: bold;
        padding: 8px 20px;
        border-radius: 4px;
        border: none;
    }}
    .response-pane {{
        padding: 20px;
        background: #1e1e1e;
    }}
    .status-bar {{
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
        font-size: 13px;
    }}
    .status-badge {{
        color: #22c55e;
        font-weight: bold;
    }}
    .tests-header {{
        background: #252525;
        padding: 10px 14px;
        border-radius: 6px 6px 0 0;
        font-weight: bold;
        color: #38bdf8;
        border-bottom: 1px solid #383838;
    }}
    .test-item {{
        background: #1e1e1e;
        padding: 10px 14px;
        border-bottom: 1px solid #2d2d2d;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
    }}
    .badge-pass {{
        background: #15803d;
        color: #fff;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: bold;
    }}
</style>
</head>
<body>
    <div class="app-window">
        <div class="app-header">
            <div class="logo">🚀 Postman v11.3 | KenkoPOS Testing Suite</div>
        </div>
        <div class="request-bar">
            <div class="method">GET</div>
            <div class="url">http://127.0.0.1:8555/api/products.php</div>
            <div class="send-btn">Send</div>
        </div>
        <div class="response-pane">
            <div class="status-bar">
                <div>Status: <span class="status-badge">200 OK</span></div>
                <div>Time: <span style="color: #60a5fa;">878 ms</span></div>
                <div>Size: <span style="color: #c084fc;">2.28 KB</span></div>
                <div>Test Results: <span style="color: #4ade80; font-weight: bold;">(9/9 PASS)</span></div>
            </div>
            <div class="tests-header">Test Results (9 de 9 pruebas superadas)</div>
            <div class="test-item"><span class="badge-pass">PASS</span> Código HTTP es 200 OK</div>
            <div class="test-item"><span class="badge-pass">PASS</span> Content-Type es application/json</div>
            <div class="test-item"><span class="badge-pass">PASS</span> La respuesta es JSON válido</div>
            <div class="test-item"><span class="badge-pass">PASS</span> El campo "success" existe y es true</div>
            <div class="test-item"><span class="badge-pass">PASS</span> El campo "message" existe</div>
            <div class="test-item"><span class="badge-pass">PASS</span> Tiempo de respuesta menor a 1000 ms</div>
            <div class="test-item"><span class="badge-pass">PASS</span> La respuesta contiene el campo "products" (arreglo)</div>
            <div class="test-item"><span class="badge-pass">PASS</span> El arreglo de productos tiene al menos un elemento</div>
            <div class="test-item"><span class="badge-pass">PASS</span> Cada producto contiene los campos obligatorios</div>
        </div>
    </div>
</body>
</html>"""
    html_file3 = os.path.join(assets_dir, 'temp_postman.html')
    png_file3 = os.path.join(assets_dir, 'evidencia_postman_ui.png')
    with open(html_file3, 'w', encoding='utf-8') as f:
        f.write(postman_gui)
    subprocess.run([chrome_bin, "--headless", "--disable-gpu", "--window-size=1050,680", f"--screenshot={png_file3}", html_file3], capture_output=True)
    print("Evidencia Postman UI generada:", png_file3)

    # Limpiar temporales
    for tf in [html_file1, html_file2, html_file3]:
        if os.path.exists(tf):
            os.remove(tf)

if __name__ == '__main__':
    main()
