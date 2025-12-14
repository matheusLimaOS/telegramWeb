import type { BoxProps } from '@mui/material'
import { Box } from '@mui/material'

function FormWrapper(props: BoxProps<'form'>) {
  return (
    <Box
      component="form"
      style={{
        display: 'flex',
        flexDirection: 'column',
        gap: '10px',
        alignItems: 'center',
      }}
      {...props}
    >
      {props.children}
    </Box>
  )
}

export default FormWrapper
