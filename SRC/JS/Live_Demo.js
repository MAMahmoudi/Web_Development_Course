// Live demos: code examples become editors, and their result is updated as the visitor types.
//
// 1. Examples with a result (div.card, div.Vertical_Demo)
//   - each code block (div.HTML_Code / CSS_Code / JS_Code) becomes an editable pane;
//     its language comes from the label above it (h2.Code_Tag: "HTML", "CSS", "JavaScript"...)
//   - JavaScript examples often only show the script: the HTML of their Result block becomes an HTML pane
//   - the result is a sandboxed <iframe> built from the panes, with the stylesheets of the page
//   - data-live-base="SRC/JS/" on the demo: folder from which its relative URLs are resolved
//     (files loaded with AJAX); by default, the folder of the page
//   Examples in PHP (or without a result) stay as they are: PHP cannot run in the browser.
//
// 2. JavaScript snippets (pre.prettyprint.lang-js outside the examples above)
//   - the snippet becomes an editor with a console: console.log/info/warn/error, errors,
//     and the value of the last expression
//   - <template data-live-html> right after the snippet: an HTML page for the snippet to work on
//     (shown above the console, editable in an HTML tab)
//   - not made live: JSON, Node.js modules (import/export), HTML, snippets of the lab pages
//     (listings of the project files); data-live="off" / data-live="on" on the <pre> forces the choice

const CDN = "https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/";
const JQUERY = "https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js";
const DELAY = 350; // ms without typing before the result is updated
const MAX_LINES = 200; // lines kept in a console
const LANGS = {
  html: { label: "HTML", mode: "htmlmixed" },
  css: { label: "CSS", mode: "css" },
  js: { label: "JavaScript", mode: "javascript" },
};

// ------------------------------------------------------------------ reading the code

/** Text of a code block: <br> become new lines, entities are decoded, common indentation removed. */
function code_text(block) {
  const pre = block.matches("pre") ? block : block.querySelector("pre") || block;
  const copy = pre.cloneNode(true);
  // a <br> followed by a real line break is the same line break: count it once
  copy.querySelectorAll("br").forEach((br) => {
    const next = br.nextSibling;
    const followed = next && next.nodeType === Node.TEXT_NODE && /^[ \t]*\n/.test(next.textContent);
    br.replaceWith(followed ? "" : "\n");
  });
  return dedent(copy.textContent.replace(/\n{3,}/g, "\n\n"));
}

function dedent(text) {
  const lines = text.replace(/\t/g, "  ").split("\n");
  while (lines.length && !lines[0].trim()) lines.shift();
  while (lines.length && !lines[lines.length - 1].trim()) lines.pop();
  const indents = lines.filter((l) => l.trim()).map((l) => l.match(/^ */)[0].length);
  const cut = indents.length ? Math.min(...indents) : 0;
  return lines.map((l) => l.slice(cut).replace(/\s+$/, "")).join("\n");
}

function language_of(label, text) {
  const name = label.trim().toLowerCase();
  if (name.includes("php") || name === "result") return null;
  if (name.includes("css")) return "css";
  if (name === "js" || name.includes("javascript")) {
    // a "JavaScript" example written with its HTML (<script>, <button>...) is an HTML document
    return /^\s*<[a-z!]/i.test(text) ? "html" : "js";
  }
  if (name.includes("html")) return "html";
  return null; // "Explanation", "Procedural"...: not a runnable example
}

function add(panes, lang, code) {
  const same = panes.find((p) => p.lang === lang);
  if (same) same.code += "\n\n" + code;
  else panes.push({ lang, code });
}

function sort_panes(panes) {
  const order = ["html", "css", "js"];
  return panes.sort((a, b) => order.indexOf(a.lang) - order.indexOf(b.lang));
}

/** The panes of an example with a result, or null when it cannot run in the browser. */
function read_demo(demo) {
  // the block shown next to the code must be a result (not an "Explanation")
  const result = demo.querySelector(":scope > .Result_Demo");
  const result_tag = result && result.previousElementSibling;
  if (!result || !result_tag || !/result/i.test(result_tag.textContent)) return null;

  const panes = [];
  for (const block of demo.querySelectorAll(":scope > .HTML_Code, :scope > .CSS_Code, :scope > .JS_Code")) {
    const tag = block.previousElementSibling;
    const label = tag && tag.classList.contains("Code_Tag") ? tag.textContent : "";
    let text = code_text(block);
    const lang = language_of(label, text);
    if (!lang) return null;
    // a script followed by the HTML it works on: the HTML goes to its own pane
    if (lang === "js") {
      const lines = text.split("\n");
      const start = lines.findIndex(
        (line, i) => /^\s*<[a-z!]/i.test(line) && lines.slice(i).every((l) => !l.trim() || /^\s*</.test(l))
      );
      if (start > 0) {
        add(panes, "html", dedent(lines.slice(start).join("\n")));
        text = dedent(lines.slice(0, start).join("\n"));
      }
    }
    add(panes, lang, text);
  }
  if (!panes.length) return null;

  // a script alone needs the elements it works on: they are in the Result block
  if (!panes.some((p) => p.lang === "html")) {
    let html = dedent(result.innerHTML);
    if (result.id) html = `<div id="${result.id}">\n${html}\n</div>`;
    panes.unshift({ lang: "html", code: html });
  }
  return sort_panes(panes);
}

/** The panes of a JavaScript snippet, or null when it is not runnable JavaScript. */
function read_snippet(pre) {
  if (pre.dataset.live === "off") return null;
  if (pre.dataset.live !== "on") {
    if (/\/Lab_\d+\.html$/.test(location.pathname)) return null; // listings of the project files
  }
  const code = code_text(pre);
  if (!code.trim()) return null;
  if (pre.dataset.live !== "on") {
    if (/^\s*</.test(code) || /<\?php/.test(code)) return null; // HTML or PHP
    if (/^\s*(import|export)\s/m.test(code)) return null; // a module (Node.js...)
    try {
      JSON.parse(code);
      return null; // data, not code
    } catch {
      // JavaScript: continue
    }
  }
  const panes = [{ lang: "js", code }];
  const holder = pre.closest(".Code_Block") || pre;
  const context = holder.nextElementSibling;
  if (context && context.matches("template[data-live-html]")) {
    panes.unshift({ lang: "html", code: dedent(context.innerHTML) });
  }
  return panes;
}

// ------------------------------------------------------------------ the document shown in the result

function page_stylesheets() {
  return [...document.querySelectorAll('link[rel="stylesheet"]')]
    .map((link) => link.href)
    .filter((href) => href && !/Nav_Bar\.css|Live_Demo\.css|fonts\.googleapis|codemirror/.test(href));
}

// Runs inside the frame of a console demo: sends console calls and errors to the page.
const CONSOLE_BRIDGE = `
  let __logged = 0;
  const __send = (type, values) => {
    __logged++;
    parent.postMessage({ live_demo: __ID, log: { type, text: values.map((v) => __show(v, 0)).join(" ") } }, "*");
  };
  function __show(v, depth) {
    if (typeof v === "string") return depth ? JSON.stringify(v) : v;
    if (typeof v === "function") return "ƒ " + (v.name || "anonymous") + "()";
    if (v === null || typeof v !== "object") return typeof v === "bigint" ? v + "n" : String(v);
    if (v instanceof Error) return v.name + ": " + v.message;
    if (v instanceof Element) {
      // like the console of the browsers: a long element is shown as <tag ...>…</tag>
      const h = v.outerHTML;
      if (h.length <= 100 || !v.children.length) return h.length > 160 ? h.slice(0, 160) + "…" : h;
      const open = h.slice(0, h.indexOf(">") + 1);
      return open + "…</" + v.localName + ">";
    }
    if (v instanceof Node) return "#" + v.nodeName.toLowerCase().replace(/^#/, "") + (v.nodeValue && v.nodeValue.trim() ? " " + JSON.stringify(v.nodeValue.trim()) : "");
    if (v instanceof NodeList || v instanceof HTMLCollection) return v.constructor.name + "(" + v.length + ") [" + [...v].map((n) => __show(n, depth + 1)).join(", ") + "]";
    if (depth > 2) return Array.isArray(v) ? "[…]" : "{…}";
    if (Array.isArray(v)) return "[" + v.map((x) => __show(x, depth + 1)).join(", ") + "]";
    if (v instanceof Map) return "Map(" + v.size + ") {" + [...v].map(([k, x]) => __show(k, depth + 1) + " => " + __show(x, depth + 1)).join(", ") + "}";
    if (v instanceof Set) return "Set(" + v.size + ") {" + [...v].map((x) => __show(x, depth + 1)).join(", ") + "}";
    if (v instanceof Date) return v.toString();
    try { return "{" + Object.entries(v).map(([k, x]) => k + ": " + __show(x, depth + 1)).join(", ") + "}"; } catch { return String(v); }
  }
  for (const type of ["log", "info", "warn", "error", "debug", "table"]) {
    const original = console[type];
    console[type] = (...values) => { __send(type === "debug" || type === "table" ? "log" : type, values); original.apply(console, values); };
  }
  addEventListener("error", (e) => __send("error", [e.error || e.message]));
  addEventListener("unhandledrejection", (e) => __send("error", ["Uncaught (in promise) " + __show(e.reason, 0)]));
  document.currentScript.remove(); // the visitor's code sees the document without the tools of the demo
`;

function build_document(panes, id, base, mode) {
  const get = (lang) => panes.filter((p) => p.lang === lang).map((p) => p.code).join("\n");
  const html = get("html"),
    css = get("css"),
    js = get("js");
  const needs_jquery = /\$\(|\$\.|jQuery/.test(html + js);
  const links = page_stylesheets().map((href) => `<link rel="stylesheet" href="${href}">`).join("\n");
  // errors of the visitor's code are sent to the page, which shows them above the result;
  // links to "#id" stay inside the example (because of <base>, they would load the page instead)
  const report = `<script>
    ${mode === "console" ? CONSOLE_BRIDGE.replace("__ID", id) : `addEventListener("error", (e) => parent.postMessage({ live_demo: ${id}, error: e.message + (e.lineno ? " (line " + e.lineno + ")" : "") }, "*"));`}
    addEventListener("click", (e) => {
      const a = e.target.closest && e.target.closest('a[href^="#"]');
      if (!a) return;
      e.preventDefault();
      const target = document.getElementById(decodeURIComponent(a.getAttribute("href").slice(1)));
      if (target) target.scrollIntoView({ behavior: "smooth" });
    });
  <\/script>`;
  // console demos: the code is evaluated, so that the value of its last expression can be shown
  const run =
    mode === "console"
      ? `<script>
  document.currentScript.remove();
  try {
    const __value = (0, eval)(${JSON.stringify(js).replace(/<\/(script)/gi, "<\\/$1")});
    // the value of the last expression, when the code printed nothing itself; not for objects that only
    // describe work in progress (jQuery objects, promises, AJAX requests): they would only show their internals
    const __noise = __value && typeof __value === "object" && (__value.jquery || typeof __value.then === "function" || "readyState" in __value);
    if (__value !== undefined && !__noise && !__logged) __send("result", [__value]);
  } catch (error) {
    __send("error", [error]);
  }
  parent.postMessage({ live_demo: ${id}, done: true }, "*");
<\/script>`
      : js
        ? `<script>\n${js}\n<\/script>`
        : "";
  return `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<base href="${base}">
${report}
${links}
<style>
  html, body { background: #fff; }
  body { margin: 12px; font-family: "Source Sans Pro", sans-serif; font-size: 1.2rem; }
</style>
${needs_jquery ? `<script src="${JQUERY}"><\/script>` : ""}
<style>
${css}
</style>
</head>
<body>
${html}
${run}
</body>
</html>`;
}

// ------------------------------------------------------------------ editors

let codemirror_ready = null;

function load(url, type) {
  return new Promise((resolve, reject) => {
    const element = document.createElement(type === "css" ? "link" : "script");
    if (type === "css") {
      element.rel = "stylesheet";
      element.href = url;
    } else {
      element.src = url;
    }
    element.onload = resolve;
    element.onerror = reject;
    document.head.appendChild(element);
  });
}

/** Loads CodeMirror once; resolves to null if the CDN is not reachable (plain text areas are used). */
function load_codemirror() {
  if (!codemirror_ready) {
    codemirror_ready = (async () => {
      try {
        await Promise.all([load(CDN + "codemirror.min.css", "css"), load(CDN + "theme/material-darker.min.css", "css")]);
        await load(CDN + "codemirror.min.js");
        for (const file of ["mode/xml/xml", "mode/javascript/javascript", "mode/css/css", "mode/htmlmixed/htmlmixed",
          "addon/edit/closebrackets", "addon/fold/xml-fold", "addon/edit/closetag"]) {
          await load(CDN + file + ".min.js");
        }
        return window.CodeMirror;
      } catch {
        return null;
      }
    })();
  }
  return codemirror_ready;
}

function create_editor(CM, parent, pane, on_change) {
  if (CM) {
    const editor = CM(parent, {
      value: pane.code,
      mode: LANGS[pane.lang].mode,
      theme: "material-darker",
      lineNumbers: true,
      lineWrapping: true,
      tabSize: 2,
      indentUnit: 2,
      autoCloseBrackets: true,
      autoCloseTags: pane.lang === "html",
      viewportMargin: Infinity,
      extraKeys: { Tab: (cm) => cm.replaceSelection("  ") },
    });
    editor.on("change", on_change);
    return { get: () => editor.getValue(), set: (v) => editor.setValue(v), refresh: () => editor.refresh() };
  }
  const area = document.createElement("textarea");
  area.className = "Live_Textarea";
  area.spellcheck = false;
  area.value = pane.code;
  area.addEventListener("input", on_change);
  parent.appendChild(area);
  return { get: () => area.value, set: (v) => (area.value = v), refresh: () => {} };
}

// ------------------------------------------------------------------ one live demo

let next_id = 1;
const demos_by_id = new Map();

/**
 * Replaces `target` by a live demo of `panes`.
 * mode "page": the result is the page built from the panes (examples with a result);
 * mode "console": the result is a console, and the page only when the snippet has an HTML context.
 */
function make_live(target, panes, CM, mode = "page") {
  const id = next_id++;
  const original = panes.map((p) => ({ ...p }));
  const has_page = mode === "page" || panes.some((p) => p.lang === "html");
  // relative URLs of the example (files loaded with AJAX...) start from data-live-base, or from the page
  const base = new URL(target.dataset.liveBase || "", location.href).href;
  const box = document.createElement("div");
  box.className = "Live_Demo" + (mode === "console" ? " Console_Mode" : "");
  box.innerHTML = `
    <div class="Live_Editor">
      <div class="Live_Bar">
        <div class="Live_Tabs" role="tablist"></div>
        <div class="Live_Actions">
          <button type="button" class="Live_Reset" title="Back to the original code"><i class="fa-solid fa-rotate-left"></i> Reset</button>
          ${has_page ? '<button type="button" class="Live_Open" title="Open the result in a new tab"><i class="fa-solid fa-up-right-from-square"></i></button>' : ""}
        </div>
      </div>
      <div class="Live_Panes"></div>
    </div>
    <div class="Live_Result">
      <div class="Live_Bar"${has_page ? "" : " hidden"}><span class="Live_Title"><i class="fa-solid fa-play"></i> Result</span><span class="Live_Status"></span></div>
      <iframe class="Live_Frame" title="Result of the example" sandbox="allow-scripts allow-same-origin allow-modals allow-forms allow-popups"${has_page ? "" : " hidden"}></iframe>
      ${mode === "console" ? '<div class="Live_Bar Console_Bar"><span class="Live_Title"><i class="fa-solid fa-terminal"></i> Console</span></div><ol class="Live_Console" aria-live="polite"></ol>' : ""}
    </div>`;

  const tabs = box.querySelector(".Live_Tabs"),
    holder = box.querySelector(".Live_Panes"),
    frame = box.querySelector(".Live_Frame"),
    status = box.querySelector(".Live_Status"),
    output = box.querySelector(".Live_Console");
  let timer = null;

  const schedule = () => {
    clearTimeout(timer);
    status.textContent = "…";
    timer = setTimeout(render, DELAY);
  };

  const editors = panes.map((pane, index) => {
    const tab = document.createElement("button");
    tab.type = "button";
    tab.className = "Live_Tab";
    tab.setAttribute("role", "tab");
    tab.textContent = LANGS[pane.lang].label;
    tabs.appendChild(tab);
    const area = document.createElement("div");
    area.className = "Live_Pane";
    holder.appendChild(area);
    const editor = create_editor(CM, area, pane, schedule);
    tab.addEventListener("click", () => select(index));
    return { pane, tab, area, editor };
  });

  function select(index) {
    editors.forEach((e, i) => {
      e.tab.classList.toggle("Active", i === index);
      e.tab.setAttribute("aria-selected", i === index);
      e.area.hidden = i !== index;
    });
    editors[index].editor.refresh();
  }

  function current() {
    return editors.map((e) => ({ lang: e.pane.lang, code: e.editor.get() }));
  }

  function render() {
    status.textContent = "";
    status.classList.remove("Error");
    if (output) output.replaceChildren();
    frame.srcdoc = build_document(current(), id, base, mode);
  }

  // the frame follows the height of its content
  if (has_page) {
    frame.addEventListener("load", () => {
      const doc = frame.contentDocument;
      if (!doc) return;
      const fit = () => (frame.style.height = Math.min(Math.max(doc.documentElement.scrollHeight, 120), 700) + "px");
      fit();
      new ResizeObserver(fit).observe(doc.body);
    });
  }

  box.querySelector(".Live_Reset").addEventListener("click", () => {
    editors.forEach((e, i) => e.editor.set(original[i].code));
    render();
  });

  box.querySelector(".Live_Open")?.addEventListener("click", () => {
    const url = URL.createObjectURL(new Blob([build_document(current(), id, base, mode)], { type: "text/html" }));
    window.open(url, "_blank");
  });

  demos_by_id.set(id, { status, output });
  if (mode === "page") {
    target.classList.add("Live_Card");
    target.replaceChildren(box);
  } else {
    box.classList.add("Live_Snippet");
    target.replaceWith(box);
  }
  select(editors.findIndex((e) => e.pane.lang === "js") >= 0 && mode === "console" ? editors.findIndex((e) => e.pane.lang === "js") : 0);
  render();
}

/** Adds a line to the console of a demo. */
function log_line(output, type, text) {
  if (output.children.length >= MAX_LINES) {
    if (output.children.length === MAX_LINES) {
      const more = document.createElement("li");
      more.className = "Log_info";
      more.textContent = `… (only the first ${MAX_LINES} lines are shown)`;
      output.appendChild(more);
    }
    return;
  }
  const line = document.createElement("li");
  line.className = "Log_" + type;
  line.textContent = (type === "result" ? "← " : "") + text;
  output.appendChild(line);
}

// messages sent by the frames (see build_document)
addEventListener("message", (event) => {
  const demo = event.data && demos_by_id.get(event.data.live_demo);
  if (!demo) return;
  if (event.data.error) {
    demo.status.textContent = "⚠ " + event.data.error;
    demo.status.classList.add("Error");
  }
  if (event.data.log && demo.output) log_line(demo.output, event.data.log.type, event.data.log.text);
  if (event.data.done && demo.output && !demo.output.children.length) {
    log_line(demo.output, "empty", "No output. Add console.log(...) to see a value.");
  }
});

// ------------------------------------------------------------------ start

const examples = [...document.querySelectorAll("div.card, div.Vertical_Demo")]
  .map((demo) => [demo, read_demo(demo)])
  .filter(([, panes]) => panes);

const snippets = [...document.querySelectorAll("pre.prettyprint.lang-js")]
  .filter((pre) => !pre.closest(".card, .Vertical_Demo, .Live_Demo"))
  .map((pre) => [pre, read_snippet(pre)])
  .filter(([, panes]) => panes);

if (examples.length || snippets.length) {
  load(new URL("../CSS/Live_Demo.css", import.meta.url).href, "css");
  load_codemirror().then((CM) => {
    examples.forEach(([demo, panes]) => make_live(demo, panes, CM, "page"));
    // a snippet in a .Code_Block wrapper replaces the wrapper (no double border)
    snippets.forEach(([pre, panes]) => make_live(pre.closest(".Code_Block") || pre, panes, CM, "console"));
  });
}
