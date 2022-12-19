local wzeterm = require 'wezterm'

return {
    enable_wayland = true,
    font_size = 13,
    enable_tab_bar = true,
    keys = {
    	{
      	   key = '"',
      	   mods = 'CTRL|SHIFT|e',
           action = wezterm.action.SplitVertical {
              args = { 'left' }
           }
       }
    }  
}

