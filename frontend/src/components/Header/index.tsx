import { AppBar, Box, FormControl, MenuItem, Select, Toolbar } from '@mui/material'
import type { FlagIconCode } from 'react-flag-kit'
import { FlagIcon } from 'react-flag-kit'
import { useTranslation } from 'react-i18next'

type Language = {
  code: string
  label: string
  countryCode: FlagIconCode
}

const languages: Array<Language> = [
  { code: 'pt', label: 'Português', countryCode: 'BR' },
  { code: 'en', label: 'English', countryCode: 'US' },
]

const Header = () => {
  const { t, i18n } = useTranslation()

  const currentLanguage = i18n.language

  const handleChangeLanguage = (event: { target: { value: string } }) => {
    i18n.changeLanguage(event.target.value)
  }

  return (
    <AppBar color="inherit" position="fixed">
      <Toolbar sx={{ display: 'flex', justifyContent: 'space-between' }}>
        <p> Telegram Web Fé </p>
        <Box>
          <FormControl
            variant="standard"
            sx={{ minWidth: 120, display: 'flex', alignItems: 'end', justifyContent: 'end' }}
          >
            <Select
              IconComponent={() => null}
              value={currentLanguage}
              onChange={handleChangeLanguage}
              label={t('language_selector_label')}
              sx={{
                '& .MuiSelect-select': {
                  paddingRight: '14px !important',
                  paddingLeft: '14px !important',
                  display: 'flex',
                  alignItems: 'center',
                },
              }}
            >
              {languages.map((lang) => (
                <MenuItem
                  key={lang.code}
                  value={lang.code}
                  sx={{
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    gap: 1,
                    padding: '4px 0',
                    margin: '0 8px',
                  }}
                >
                  <FlagIcon code={lang.countryCode} size={26} />
                </MenuItem>
              ))}
            </Select>
          </FormControl>
        </Box>
      </Toolbar>
    </AppBar>
  )
}

export default Header
