ace.define("ace/mode/blade_highlight_rules", [
    "require", "exports", "module",
    "ace/lib/oop",
    "ace/mode/html_highlight_rules",
    "ace/mode/text_highlight_rules"
], function(require, exports, module) {
    "use strict";

    var oop = require("../lib/oop");
    var HtmlHighlightRules = require("./html_highlight_rules").HtmlHighlightRules;
    var TextHighlightRules = require("./text_highlight_rules").TextHighlightRules;

    var BladeHighlightRules = function() {
        HtmlHighlightRules.call(this);

        // Blade директивы
        var bladeKeywords = [
            "if", "else", "elseif", "endif", "unless", "endunless",
            "for", "endforeach", "forelse", "endforelse", "empty",
            "while", "endwhile", "switch", "case", "default", "endswitch",
            "break", "continue", "include", "includeIf", "includeWhen",
            "includeFirst", "each", "yield", "section", "show", "endsection",
            "append", "stop", "overwrite", "extends", "parent", "hasSection",
            "verbatim", "endverbatim", "php", "endphp", "json", "endjson",
            "lang", "choice", "can", "cannot", "canany", "elsecan", "elsecannot",
            "auth", "guest", "production", "env", "error", "hasError", "isset",
            "empty", "dd", "dump", "method", "csrf", "style", "script", "link",
            "route", "asset", "url", "action", "secure_asset", "secure_url"
        ];

        var keywordMapper = this.createKeywordMapper({
            "blade.directive": bladeKeywords.join("|"),
            "support.function": "config|old|request|session|trans|__|auth|route|url|asset"
        }, "identifier");

        // Добавляем правила для Blade
        this.$rules = {
            "start": [
                {
                    token: "blade.comment",
                    regex: "\\{\\{--.*?--\\}\\}",
                    next: "start"
                },
                {
                    token: "blade.escaped_echo",
                    regex: "\\{!!.*?!!\\}",
                    next: "start"
                },
                {
                    token: "blade.echo",
                    regex: "\\{\\{.*?\\}\\}",
                    next: "start"
                },
                {
                    token: function(value) {
                        if (value.startsWith('@')) {
                            return keywordMapper(value.slice(1));
                        }
                        return "blade.directive";
                    },
                    regex: "@(\\w+)",
                    next: "start"
                }
            ].concat(this.$rules.start)
        };

        // Добавляем Blade правила ко всем остальным правилам
        for (var state in this.$rules) {
            if (state !== "start") {
                this.$rules[state] = [
                    {
                        token: "blade.comment",
                        regex: "\\{\\{--.*?--\\}\\}",
                        next: state
                    },
                    {
                        token: "blade.escaped_echo",
                        regex: "\\{!!.*?!!\\}",
                        next: state
                    },
                    {
                        token: "blade.echo",
                        regex: "\\{\\{.*?\\}\\}",
                        next: state
                    },
                    {
                        token: function(value) {
                            if (value.startsWith('@')) {
                                return keywordMapper(value.slice(1));
                            }
                            return "blade.directive";
                        },
                        regex: "@(\\w+)",
                        next: state
                    }
                ].concat(this.$rules[state]);
            }
        }
    };

    oop.inherits(BladeHighlightRules, HtmlHighlightRules);
    exports.BladeHighlightRules = BladeHighlightRules;
});

// Режим для Blade
ace.define("ace/mode/blade", [
    "require", "exports", "module",
    "ace/lib/oop",
    "ace/mode/html",
    "ace/mode/blade_highlight_rules"
], function(require, exports, module) {
    "use strict";

    var oop = require("../lib/oop");
    var HtmlMode = require("./html").Mode;
    var BladeHighlightRules = require("./blade_highlight_rules").BladeHighlightRules;

    var Mode = function() {
        HtmlMode.call(this);
        this.HighlightRules = BladeHighlightRules;
    };

    oop.inherits(Mode, HtmlMode);

    (function() {
        this.$id = "ace/mode/blade";
        this.getNextLineIndent = function(state, line, tab) {
            return this.$getIndent(line);
        };
    }).call(Mode.prototype);

    exports.Mode = Mode;
});
