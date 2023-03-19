local wezterm = require 'wezterm'

return {
  enable_wayland = true,
  font_size = 13, 
  enable_tab_bar = true, 
  keys = {
    -- This will create a new split and run the `top` program inside it
    {
      key = 'e',
      mods = 'CTRL|SHIFT',
      action = wezterm.action.SplitVertical {
        domain = 'CurrentPaneDomain'
      },
    },
    {
        key = 'o',
        mods = 'CTRL|SHIFT',
        action = wezterm.action.SplitHorizontal {
          domain = 'CurrentPaneDomain'
        },
    },
    {
        key = 'x',
        mods = 'SHIFT|CTRL',
        action = wezterm.action.TogglePaneZoomState,
    },
  },
}
